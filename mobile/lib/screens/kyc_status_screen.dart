import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../constants/colors.dart';
import '../constants/constants.dart';
import '../services/api_service.dart';
import '../services/auth_service.dart';
import 'home_screen.dart';

class KycStatusScreen extends StatefulWidget {
  final bool justRegistered;

  const KycStatusScreen({super.key, this.justRegistered = false});

  @override
  State<KycStatusScreen> createState() => _KycStatusScreenState();
}

class _KycStatusScreenState extends State<KycStatusScreen> {
  Map<String, dynamic>? _kycData;
  bool _isLoading = true;
  bool _isRefreshing = false;

  @override
  void initState() {
    super.initState();
    _fetchKycStatus();
  }

  Future<void> _fetchKycStatus() async {
    setState(() => _isRefreshing = true);
    try {
      final data = await ApiService().get('user/kyc-status');
      setState(() {
        _kycData = data['data'] as Map<String, dynamic>?;
        _isLoading = false;
        _isRefreshing = false;
      });
    } catch (_) {
      setState(() {
        _isLoading = false;
        _isRefreshing = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F7FA),
      appBar: AppBar(
        title: Text('Verification Status', style: GoogleFonts.nunito(fontWeight: FontWeight.w700)),
        backgroundColor: AppColors.tealGreen,
        foregroundColor: Colors.white,
        elevation: 0,
        actions: [
          IconButton(
            onPressed: _isRefreshing ? null : _fetchKycStatus,
            icon: _isRefreshing
                ? const SizedBox(
                    width: 20,
                    height: 20,
                    child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                  )
                : const Icon(Icons.refresh, size: 22),
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: AppColors.tealGreen))
          : _kycData == null
              ? _buildErrorState()
              : RefreshIndicator(
                  onRefresh: _fetchKycStatus,
                  color: AppColors.tealGreen,
                  child: ListView(
                    padding: const EdgeInsets.all(20),
                    children: [
                      _buildStatusHeader(),
                      const SizedBox(height: 24),
                      _buildStepper(),
                      const SizedBox(height: 24),
                      _buildDocumentsList(),
                      const SizedBox(height: 24),
                      _buildActionButtons(),
                    ],
                  ),
                ),
    );
  }

  Widget _buildStatusHeader() {
    final status = _kycData?['kyc_status'] ?? 'pending';
    final role = _kycData?['role'] ?? 'tenant';
    final verificationLevel = _kycData?['verification_level'];

    Color statusColor;
    IconData statusIcon;
    String statusTitle;
    String statusMessage;

    switch (status) {
      case 'approved':
        statusColor = AppColors.success;
        statusIcon = Icons.verified;
        statusTitle = 'Verified!';
        statusMessage = 'Congratulations! Your account is verified. You can now list properties on ${AppConstants.appName}.';
        break;
      case 'rejected':
        statusColor = AppColors.error;
        statusIcon = Icons.cancel;
        statusTitle = 'Not Verified';
        statusMessage = 'Your verification was not approved. Please review your documents and resubmit.';
        break;
      case 'pending':
      default:
        statusColor = AppColors.warning;
        statusIcon = Icons.hourglass_top;
        statusTitle = 'Pending Verification';
        statusMessage = 'Your documents are being reviewed. This usually takes 24-48 hours. We\'ll notify you once verified.';
        break;
    }

    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [statusColor.withValues(alpha: 0.1), statusColor.withValues(alpha: 0.03)],
        ),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: statusColor.withValues(alpha: 0.2)),
      ),
      child: Column(
        children: [
          Container(
            width: 72,
            height: 72,
            decoration: BoxDecoration(
              color: statusColor.withValues(alpha: 0.15),
              shape: BoxShape.circle,
            ),
            child: Icon(statusIcon, size: 40, color: statusColor),
          ),
          const SizedBox(height: 16),
          Text(
            statusTitle,
            style: GoogleFonts.nunito(
              fontSize: 22,
              fontWeight: FontWeight.w900,
              color: statusColor,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            statusMessage,
            textAlign: TextAlign.center,
            style: GoogleFonts.nunito(
              fontSize: 13,
              color: AppColors.textSecondary,
              height: 1.6,
            ),
          ),
          const SizedBox(height: 16),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              _buildInfoChip('Role', _capitalize(role)),
              const SizedBox(width: 12),
              if (verificationLevel != null)
                _buildInfoChip('Level', _capitalize(verificationLevel)),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildInfoChip(String label, String value) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: AppColors.inputBorder),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Text(
            '$label: ',
            style: GoogleFonts.nunito(fontSize: 11, fontWeight: FontWeight.w600, color: AppColors.textHint),
          ),
          Text(
            value,
            style: GoogleFonts.nunito(fontSize: 11, fontWeight: FontWeight.w800, color: AppColors.textPrimary),
          ),
        ],
      ),
    );
  }

  Widget _buildStepper() {
    final status = _kycData?['kyc_status'] ?? 'pending';
    final documents = (_kycData?['documents'] as List<dynamic>?) ?? [];
    final hasDocuments = documents.isNotEmpty;

    final steps = [
      {
        'title': 'Account Created',
        'subtitle': 'Your account has been set up',
        'icon': Icons.person_add_outlined,
        'completed': true,
      },
      {
        'title': 'Documents Submitted',
        'subtitle': hasDocuments ? 'KYC documents uploaded' : 'Upload your KYC documents',
        'icon': Icons.upload_file_outlined,
        'completed': hasDocuments,
      },
      {
        'title': 'Under Review',
        'subtitle': 'Admin reviewing your documents',
        'icon': Icons.rate_review_outlined,
        'completed': hasDocuments && status == 'pending',
      },
      {
        'title': 'Verification Complete',
        'subtitle': status == 'approved' ? 'You are verified!' : 'Awaiting verification',
        'icon': status == 'approved' ? Icons.verified : Icons.verified_outlined,
        'completed': status == 'approved',
      },
    ];

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(color: Colors.black.withValues(alpha: 0.03), blurRadius: 6, offset: const Offset(0, 2)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Progress Tracker',
            style: GoogleFonts.nunito(fontSize: 16, fontWeight: FontWeight.w800, color: AppColors.textPrimary),
          ),
          const SizedBox(height: 20),
          ...List.generate(steps.length, (index) {
            final step = steps[index];
            final isCompleted = step['completed'] as bool;
            final isLast = index == steps.length - 1;
            final isActive = !isCompleted && (index == 0 || steps[index - 1]['completed'] as bool);

            Color stepColor;
            if (isCompleted) {
              stepColor = status == 'rejected' && index == 3 ? AppColors.error : AppColors.tealGreen;
            } else if (isActive) {
              stepColor = AppColors.warning;
            } else {
              stepColor = AppColors.textHint;
            }

            return Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Line + circle
                SizedBox(
                  width: 32,
                  child: Column(
                    children: [
                      Container(
                        width: 32,
                        height: 32,
                        decoration: BoxDecoration(
                          color: isCompleted ? stepColor : stepColor.withValues(alpha: 0.1),
                          shape: BoxShape.circle,
                          border: isActive ? Border.all(color: stepColor, width: 2) : null,
                        ),
                        child: isCompleted
                            ? Icon(Icons.check, size: 16, color: Colors.white)
                            : Icon(step['icon'] as IconData, size: 16, color: stepColor),
                      ),
                      if (!isLast)
                        Container(
                          width: 2,
                          height: 40,
                          color: isCompleted ? AppColors.tealGreen : AppColors.inputBorder,
                        ),
                    ],
                  ),
                ),
                const SizedBox(width: 16),
                // Content
                Expanded(
                  child: Padding(
                    padding: EdgeInsets.only(bottom: isLast ? 0 : 24),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          step['title'] as String,
                          style: GoogleFonts.nunito(
                            fontSize: 14,
                            fontWeight: FontWeight.w800,
                            color: isCompleted || isActive ? AppColors.textPrimary : AppColors.textHint,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          step['subtitle'] as String,
                          style: GoogleFonts.nunito(
                            fontSize: 12,
                            color: AppColors.textSecondary,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            );
          }),
        ],
      ),
    );
  }

  Widget _buildDocumentsList() {
    final documents = (_kycData?['documents'] as List<dynamic>?) ?? [];

    if (documents.isEmpty) {
      return Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(color: Colors.black.withValues(alpha: 0.03), blurRadius: 6, offset: const Offset(0, 2)),
          ],
        ),
        child: Column(
          children: [
            Icon(Icons.document_scanner_outlined, size: 40, color: AppColors.textHint),
            const SizedBox(height: 12),
            Text(
              'No KYC Documents Yet',
              style: GoogleFonts.nunito(fontSize: 14, fontWeight: FontWeight.w700, color: AppColors.textPrimary),
            ),
            const SizedBox(height: 4),
            Text(
              'Upload your documents to start verification',
              style: GoogleFonts.nunito(fontSize: 12, color: AppColors.textSecondary),
            ),
          ],
        ),
      );
    }

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(color: Colors.black.withValues(alpha: 0.03), blurRadius: 6, offset: const Offset(0, 2)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Your Documents (${documents.length})',
            style: GoogleFonts.nunito(fontSize: 14, fontWeight: FontWeight.w800, color: AppColors.textPrimary),
          ),
          const SizedBox(height: 16),
          ...documents.map((doc) {
            final d = doc as Map<String, dynamic>;
            final docStatus = d['status'] ?? 'pending';
            Color statusColor;
            IconData statusIcon;

            switch (docStatus) {
              case 'approved':
                statusColor = AppColors.success;
                statusIcon = Icons.check_circle;
                break;
              case 'rejected':
                statusColor = AppColors.error;
                statusIcon = Icons.cancel;
                break;
              default:
                statusColor = AppColors.warning;
                statusIcon = Icons.hourglass_top;
            }

            return Container(
              margin: const EdgeInsets.only(bottom: 10),
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: AppColors.scaffoldBg,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: AppColors.inputBorder),
              ),
              child: Row(
                children: [
                  Container(
                    width: 36,
                    height: 36,
                    decoration: BoxDecoration(
                      color: statusColor.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Icon(Icons.description_outlined, size: 18, color: statusColor),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          _capitalize((d['document_type'] ?? 'document').toString().replaceAll('_', ' ')),
                          style: GoogleFonts.nunito(fontSize: 13, fontWeight: FontWeight.w700, color: AppColors.textPrimary),
                        ),
                        if (d['document_number'] != null)
                          Text(
                            'No: ${d['document_number']}',
                            style: GoogleFonts.nunito(fontSize: 11, color: AppColors.textHint),
                          ),
                      ],
                    ),
                  ),
                  Icon(statusIcon, size: 20, color: statusColor),
                ],
              ),
            );
          }),
        ],
      ),
    );
  }

  Widget _buildActionButtons() {
    final status = _kycData?['kyc_status'] ?? 'pending';
    final documents = (_kycData?['documents'] as List<dynamic>?) ?? [];

    return Column(
      children: [
        if (status != 'approved') ...[
          SizedBox(
            width: double.infinity,
            height: 52,
            child: ElevatedButton.icon(
              onPressed: () {
                // Navigate to KYC upload screen
                Navigator.pushNamed(context, '/kyc-upload');
              },
              icon: const Icon(Icons.upload_file, size: 20),
              label: Text(
                documents.isEmpty ? 'Upload KYC Documents' : 'Upload More Documents',
                style: GoogleFonts.nunito(fontWeight: FontWeight.w700, fontSize: 14),
              ),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.tealGreen,
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
            ),
          ),
          const SizedBox(height: 12),
        ],
        if (status == 'approved') ...[
          SizedBox(
            width: double.infinity,
            height: 52,
            child: ElevatedButton.icon(
              onPressed: () {
                Navigator.pushAndRemoveUntil(
                  context,
                  MaterialPageRoute(builder: (_) => const HomeScreen()),
                  (route) => false,
                );
              },
              icon: const Icon(Icons.home, size: 20),
              label: Text(
                'Go to Home',
                style: GoogleFonts.nunito(fontWeight: FontWeight.w700, fontSize: 14),
              ),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.tealGreen,
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
            ),
          ),
          const SizedBox(height: 12),
        ],
        TextButton(
          onPressed: () {
            Navigator.pushAndRemoveUntil(
              context,
              MaterialPageRoute(builder: (_) => const HomeScreen()),
              (route) => false,
            );
          },
          child: Text(
            status == 'approved' ? 'Start Listing Properties' : 'Continue to Home',
            style: GoogleFonts.nunito(fontWeight: FontWeight.w700, fontSize: 13, color: AppColors.tealGreen),
          ),
        ),
      ],
    );
  }

  Widget _buildErrorState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(Icons.cloud_off, size: 48, color: AppColors.textHint),
          const SizedBox(height: 16),
          Text(
            'Could not load status',
            style: GoogleFonts.nunito(fontSize: 16, fontWeight: FontWeight.w700, color: AppColors.textPrimary),
          ),
          const SizedBox(height: 8),
          Text(
            'Please check your connection and try again',
            style: GoogleFonts.nunito(fontSize: 13, color: AppColors.textSecondary),
          ),
          const SizedBox(height: 20),
          ElevatedButton(
            onPressed: _fetchKycStatus,
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.tealGreen,
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
            child: Text('Retry', style: GoogleFonts.nunito(fontWeight: FontWeight.w700)),
          ),
        ],
      ),
    );
  }

  String _capitalize(String s) {
    if (s.isEmpty) return s;
    return s[0].toUpperCase() + s.substring(1);
  }
}
