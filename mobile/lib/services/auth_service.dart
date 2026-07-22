import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../constants/constants.dart';
import 'api_service.dart';

class AuthService {
  static final AuthService _instance = AuthService._internal();
  factory AuthService() => _instance;
  AuthService._internal();

  final ApiService _api = ApiService();

  Future<bool> login(String email, String password) async {
    final data = await _api.post('auth/login', body: {
      'email': email,
      'password': password,
    });

    if (data['success'] == true) {
      final token = data['token'] as String;
      final user = data['user'] as Map<String, dynamic>;
      await _saveSession(token, user);
      return true;
    }
    return false;
  }

  Future<bool> register(
    String name,
    String email,
    String password, {
    required String phone,
    String role = 'tenant',
    String? businessName,
    String? region,
    String? district,
    String? address,
  }) async {
    final body = {
      'name': name,
      'email': email,
      'password': password,
      'password_confirmation': password,
      'phone': phone,
    };

    if (role != 'tenant') {
      body['role'] = role;
      if (businessName != null) body['business_name'] = businessName;
      if (region != null) body['region'] = region;
      if (district != null) body['district'] = district;
      if (address != null) body['address'] = address;
    }

    final data = await _api.post('auth/register', body: body);

    if (data['success'] == true) {
      final token = data['token'] as String;
      final user = data['user'] as Map<String, dynamic>;
      await _saveSession(token, user);
      return true;
    }
    return false;
  }

  Future<void> logout() async {
    try {
      await _api.post('auth/logout');
    } catch (_) {}
    await _clearSession();
  }

  Future<Map<String, dynamic>?> getUser() async {
    final prefs = await SharedPreferences.getInstance();
    final userJson = prefs.getString(AppConstants.userKey);
    if (userJson != null) {
      return jsonDecode(userJson) as Map<String, dynamic>;
    }
    return null;
  }

  Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(AppConstants.tokenKey);
  }

  Future<bool> isLoggedIn() async {
    final token = await getToken();
    if (token == null) return false;
    _api.setToken(token);
    return true;
  }

  Future<void> _saveSession(String token, Map<String, dynamic> user) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(AppConstants.tokenKey, token);
    await prefs.setString(AppConstants.userKey, jsonEncode(user));
    _api.setToken(token);
  }

  Future<void> updateUser(Map<String, dynamic> user) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(AppConstants.userKey, jsonEncode(user));
  }

  Future<void> _clearSession() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(AppConstants.tokenKey);
    await prefs.remove(AppConstants.userKey);
    _api.clearToken();
  }
}
