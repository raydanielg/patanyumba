import 'dart:convert';
import 'package:http/http.dart' as http;
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

  Future<Map<String, dynamic>> get(String endpoint) async {
    final response = await http
        .get(
          Uri.parse('${AppConstants.baseUrl}/$endpoint'),
          headers: _headers,
        )
        .timeout(AppConstants.apiTimeout);
    return _handleResponse(response);
  }

  Future<Map<String, dynamic>> post(String endpoint, {Map<String, dynamic>? body}) async {
    final response = await http
        .post(
          Uri.parse('${AppConstants.baseUrl}/$endpoint'),
          headers: _headers,
          body: body != null ? jsonEncode(body) : null,
        )
        .timeout(AppConstants.apiTimeout);
    return _handleResponse(response);
  }

  Future<Map<String, dynamic>> put(String endpoint, {Map<String, dynamic>? body}) async {
    final response = await http
        .put(
          Uri.parse('${AppConstants.baseUrl}/$endpoint'),
          headers: _headers,
          body: body != null ? jsonEncode(body) : null,
        )
        .timeout(AppConstants.apiTimeout);
    return _handleResponse(response);
  }

  Future<Map<String, dynamic>> delete(String endpoint) async {
    final response = await http
        .delete(
          Uri.parse('${AppConstants.baseUrl}/$endpoint'),
          headers: _headers,
        )
        .timeout(AppConstants.apiTimeout);
    return _handleResponse(response);
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
