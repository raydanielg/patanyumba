import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter/foundation.dart';
import '../constants/constants.dart';

class ApiService {
  static final ApiService _instance = ApiService._internal();
  factory ApiService() => _instance;
  ApiService._internal();

  String? _token;

  void setToken(String token) {
    _token = token;
  }

  void clearToken() {
    _token = null;
  }

  String? get token => _token;

  Map<String, String> get _headers => {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        if (_token != null) 'Authorization': 'Bearer $_token',
      };

  void _logRequest(String method, String url, {Map<String, dynamic>? body, int? status, int? durationMs, String? error}) {
    if (!kDebugMode) return;

    const green = '\x1B[32m';
    const red = '\x1B[31m';
    const yellow = '\x1B[33m';
    const cyan = '\x1B[36m';
    const gray = '\x1B[90m';
    const reset = '\x1B[0m';

    String methodStr;
    switch (method) {
      case 'GET':
        methodStr = '$cyan$method$reset';
        break;
      case 'POST':
        methodStr = '$green$method$reset';
        break;
      case 'PUT':
        methodStr = '$yellow$method$reset';
        break;
      case 'DELETE':
        methodStr = '$red$method$reset';
        break;
      default:
        methodStr = method;
    }

    String statusStr = '';
    if (status != null) {
      if (status >= 200 && status < 300) {
        statusStr = '$green$status$reset';
      } else if (status >= 400 && status < 500) {
        statusStr = '$yellow$status$reset';
      } else if (status >= 500) {
        statusStr = '$red$status$reset';
      } else {
        statusStr = '$status';
      }
    }

    final durationStr = durationMs != null ? '$gray${durationMs}ms$reset' : '';
    final errorStr = error != null ? '$red ERROR: $error$reset' : '';

    String bodyStr = '';
    if (body != null) {
      final sanitized = Map<String, dynamic>.from(body)
        ..remove('password')
        ..remove('password_confirmation')
        ..remove('current_password');
      bodyStr = '$gray${jsonEncode(sanitized)}$reset';
    }

    debugPrint('┌─────────────────────────────────────────────');
    debugPrint('│ $methodStr  $url  $statusStr  $durationStr');
    if (bodyStr.isNotEmpty) debugPrint('│ Body: $bodyStr');
    if (errorStr.isNotEmpty) debugPrint('│ $errorStr');
    debugPrint('└─────────────────────────────────────────────');
  }

  Future<Map<String, dynamic>> get(String endpoint) async {
    final url = '${AppConstants.baseUrl}/$endpoint';
    final sw = Stopwatch()..start();
    try {
      final response = await http
          .get(Uri.parse(url), headers: _headers)
          .timeout(AppConstants.apiTimeout);
      sw.stop();
      _logRequest('GET', url, status: response.statusCode, durationMs: sw.elapsedMilliseconds);
      return _handleResponse(response);
    } catch (e) {
      sw.stop();
      _logRequest('GET', url, error: e.toString(), durationMs: sw.elapsedMilliseconds);
      rethrow;
    }
  }

  Future<Map<String, dynamic>> post(String endpoint, {Map<String, dynamic>? body}) async {
    final url = '${AppConstants.baseUrl}/$endpoint';
    final sw = Stopwatch()..start();
    try {
      final response = await http
          .post(Uri.parse(url), headers: _headers, body: body != null ? jsonEncode(body) : null)
          .timeout(AppConstants.apiTimeout);
      sw.stop();
      _logRequest('POST', url, body: body, status: response.statusCode, durationMs: sw.elapsedMilliseconds);
      return _handleResponse(response);
    } catch (e) {
      sw.stop();
      _logRequest('POST', url, body: body, error: e.toString(), durationMs: sw.elapsedMilliseconds);
      rethrow;
    }
  }

  Future<Map<String, dynamic>> put(String endpoint, {Map<String, dynamic>? body}) async {
    final url = '${AppConstants.baseUrl}/$endpoint';
    final sw = Stopwatch()..start();
    try {
      final response = await http
          .put(Uri.parse(url), headers: _headers, body: body != null ? jsonEncode(body) : null)
          .timeout(AppConstants.apiTimeout);
      sw.stop();
      _logRequest('PUT', url, body: body, status: response.statusCode, durationMs: sw.elapsedMilliseconds);
      return _handleResponse(response);
    } catch (e) {
      sw.stop();
      _logRequest('PUT', url, body: body, error: e.toString(), durationMs: sw.elapsedMilliseconds);
      rethrow;
    }
  }

  Future<Map<String, dynamic>> delete(String endpoint) async {
    final url = '${AppConstants.baseUrl}/$endpoint';
    final sw = Stopwatch()..start();
    try {
      final response = await http
          .delete(Uri.parse(url), headers: _headers)
          .timeout(AppConstants.apiTimeout);
      sw.stop();
      _logRequest('DELETE', url, status: response.statusCode, durationMs: sw.elapsedMilliseconds);
      return _handleResponse(response);
    } catch (e) {
      sw.stop();
      _logRequest('DELETE', url, error: e.toString(), durationMs: sw.elapsedMilliseconds);
      rethrow;
    }
  }

  Map<String, dynamic> _handleResponse(http.Response response) {
    final data = jsonDecode(response.body) as Map<String, dynamic>;

    if (response.statusCode >= 200 && response.statusCode < 300) {
      return data;
    }

    if (response.statusCode == 401) {
      throw ApiException('Unauthorized. Please login again.', 401);
    }

    if (response.statusCode == 422) {
      final errors = data['errors'] as Map<String, dynamic>?;
      if (errors != null && errors.isNotEmpty) {
        final firstError = (errors.values.first as List).first.toString();
        throw ApiException(firstError, 422);
      }
      throw ApiException(data['message'] ?? 'Validation error', 422);
    }

    throw ApiException(
      data['message'] ?? 'Something went wrong. Please try again.',
      response.statusCode,
    );
  }
}

class ApiException implements Exception {
  final String message;
  final int statusCode;

  ApiException(this.message, this.statusCode);

  @override
  String toString() => message;
}
