import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:video_player/video_player.dart';
import 'package:url_launcher/url_launcher.dart';
import '../constants/colors.dart';
import '../services/api_service.dart';

class PropertyDetailScreen extends StatefulWidget {
  final int propertyId;

  const PropertyDetailScreen({super.key, required this.propertyId});

  @override
  State<PropertyDetailScreen> createState() => _PropertyDetailScreenState();
}

class _PropertyDetailScreenState extends State<PropertyDetailScreen> {
  Map<String, dynamic>? _property;
  bool _isLoading = true;
  int _currentImageIndex = 0;
  List<Map<String, dynamic>> _recommended = [];

  @override
  void initState() {
    super.initState();
    _fetchProperty();
  }

  Future<void> _fetchProperty() async {
    try {
      final data = await ApiService().get('properties/${widget.propertyId}');
      setState(() {
        _property = data['data'] as Map<String, dynamic>?;
        _isLoading = false;
      });
      if (_property != null) {
        _fetchRecommended();
      }
    } catch (_) {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _fetchRecommended() async {
    try {
      final region = _property!['region'] ?? '';
      final categories = (_property!['categories'] as List<dynamic>?) ?? [];
      final categoryId = categories.isNotEmpty ? categories[0]['id'] : null;
      String endpoint = 'properties?per_page=5';
      if (region.isNotEmpty) endpoint += '&region=${Uri.encodeComponent(region.toString())}';
      if (categoryId != null) endpoint += '&category_id=$categoryId';
      final data = await ApiService().get(endpoint);
      final props = (data['data'] as List<dynamic>?) ?? [];
      setState(() {
        _recommended = props
            .cast<Map<String, dynamic>>()
            .where((p) => p['id'] != widget.propertyId)
            .take(4)
            .toList();
      });
    } catch (_) {}
  }

  String _formatPrice(dynamic price) {
    if (price == null) return 'N/A';
    final p = double.tryParse(price.toString()) ?? 0;
    if (p >= 1000000) {
      return '${(p / 1000000).toStringAsFixed(1)}M TZS';
    } else if (p >= 1000) {
      return '${(p / 1000).toStringAsFixed(0)}K TZS';
    }
    return '$p TZS';
  }

  IconData _getAmenityIcon(String amenity) {
    final a = amenity.toLowerCase();
    if (a.contains('wifi') || a.contains('internet')) return Icons.wifi;
    if (a.contains('pool') || a.contains('swim')) return Icons.pool;
    if (a.contains('parking') || a.contains('garage')) return Icons.local_parking;
    if (a.contains('garden') || a.contains('yard')) return Icons.yard;
    if (a.contains('security') || a.contains('guard')) return Icons.security;
    if (a.contains('water') || a.contains('tank')) return Icons.water_drop;
    if (a.contains('electric') || a.contains('power') || a.contains('solar')) return Icons.bolt;
    if (a.contains('ac') || a.contains('air')) return Icons.ac_unit;
    if (a.contains('furnish')) return Icons.chair;
    if (a.contains('tv') || a.contains('satellite') || a.contains('cable')) return Icons.tv;
    if (a.contains('kitchen')) return Icons.kitchen;
    if (a.contains('laundry') || a.contains('wash') || a.contains('machine')) return Icons.local_laundry_service;
    if (a.contains('gym') || a.contains('fitness')) return Icons.fitness_center;
    if (a.contains('elevator') || a.contains('lift')) return Icons.elevator;
    if (a.contains('balcony') || a.contains('terrace')) return Icons.balcony;
    if (a.contains('toilet') || a.contains('bathroom') || a.contains('bath')) return Icons.bathroom;
    if (a.contains('fence') || a.contains('wall')) return Icons.fence;
    if (a.contains('gate')) return Icons.login;
    if (a.contains('store') || a.contains('shop')) return Icons.store;
    if (a.contains('play') || a.contains('kids')) return Icons.child_care;
    return Icons.check_circle_outline;
  }

  void _playVideo(BuildContext context, String? videoUrl) {
    if (videoUrl == null || videoUrl.isEmpty) return;
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => _VideoPlayerScreen(videoUrl: videoUrl),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _property == null
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.error_outline, size: 48, color: AppColors.textHint),
                      const SizedBox(height: 12),
                      Text('Failed to load property', style: GoogleFonts.nunito(color: AppColors.textHint)),
                      const SizedBox(height: 16),
                      ElevatedButton(onPressed: () => Navigator.pop(context), child: const Text('Go Back')),
                    ],
                  ),
                )
              : _buildDetail(),
    );
  }

  Widget _buildDetail() {
    final allMedia = (_property!['images'] as List<dynamic>?) ?? [];
    final images = allMedia.where((m) => (m['media_type'] ?? 'image') == 'image').toList();
    final videos = allMedia.where((m) => (m['media_type'] ?? 'image') == 'video').toList();
    final title = _property!['title'] ?? 'Untitled';
    final description = _property!['description'] ?? '';
    final price = _property!['price'];
    final region = _property!['region'] ?? '';
    final district = _property!['district'] ?? '';
    final ward = _property!['ward'] ?? '';
    final bedrooms = _property!['bedrooms'];
    final bathrooms = _property!['bathrooms'];
    final areaSqm = _property!['area_sqm'];
    final isFurnished = _property!['is_furnished'] == true;
    final amenities = (_property!['amenities'] as List<dynamic>?) ?? [];
    final contactPhone = _property!['contact_phone'] ?? '';
    final user = _property!['user'] as Map<String, dynamic>?;
    final ownerName = user?['name'] ?? 'Property Owner';
    final propertyType = _property!['property_type'] ?? '';
    final listingType = _property!['listing_type'] ?? '';
    final rentalPeriod = _property!['rental_period'] ?? 'month';
    final isAvailable = _property!['is_available'] == true;

    return CustomScrollView(
      slivers: [
        // Image gallery
        SliverAppBar(
          expandedHeight: 280,
          pinned: true,
          leading: Padding(
            padding: const EdgeInsets.all(8),
            child: CircleAvatar(
              backgroundColor: Colors.black.withValues(alpha: 0.4),
              child: IconButton(
                icon: const Icon(Icons.arrow_back, color: Colors.white, size: 20),
                onPressed: () => Navigator.pop(context),
              ),
            ),
          ),
          flexibleSpace: FlexibleSpaceBar(
            background: allMedia.isNotEmpty
                ? PageView.builder(
                    itemCount: allMedia.length,
                    onPageChanged: (i) => setState(() => _currentImageIndex = i),
                    itemBuilder: (context, index) {
                      final media = allMedia[index];
                      final mediaType = media['media_type'] ?? 'image';
                      final url = mediaType == 'video'
                          ? (media['thumbnail_link'] ?? media['image_url'] ?? media['url'])
                          : (media['url'] ?? media['image_url']);
                      final videoUrl = mediaType == 'video'
                          ? (media['video_link'] ?? media['video_url'])
                          : null;
                      return Stack(
                        fit: StackFit.expand,
                        children: [
                          if (url != null && url.isNotEmpty)
                            Image.network(
                              url,
                              fit: BoxFit.cover,
                              errorBuilder: (_, __, ___) => Container(
                                color: AppColors.tealGreen50,
                                child: Icon(Icons.home_outlined, size: 48, color: AppColors.tealGreen),
                              ),
                            )
                          else
                            Container(
                              color: AppColors.tealGreen50,
                              child: Icon(Icons.home_outlined, size: 48, color: AppColors.tealGreen),
                            ),
                          if (mediaType == 'video')
                            Center(
                              child: GestureDetector(
                                onTap: () => _playVideo(context, videoUrl),
                                child: Container(
                                  width: 56,
                                  height: 56,
                                  decoration: BoxDecoration(
                                    color: Colors.black.withValues(alpha: 0.6),
                                    shape: BoxShape.circle,
                                    border: Border.all(color: Colors.white, width: 2),
                                  ),
                                  child: const Icon(Icons.play_arrow, color: Colors.white, size: 32),
                                ),
                              ),
                            ),
                        ],
                      );
                    },
                  )
                : Container(
                    color: AppColors.tealGreen50,
                    child: Icon(Icons.home_outlined, size: 48, color: AppColors.tealGreen),
                  ),
          ),
          actions: [
            if (allMedia.isNotEmpty)
              Padding(
                padding: const EdgeInsets.only(right: 12),
                child: Center(
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.black.withValues(alpha: 0.5),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Text(
                      '${_currentImageIndex + 1}/${allMedia.length}',
                      style: const TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.w600),
                    ),
                  ),
                ),
              ),
          ],
        ),

        // Content
        SliverToBoxAdapter(
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Title + Price
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Expanded(
                      child: Text(
                        title,
                        style: GoogleFonts.nunito(
                          fontSize: 20,
                          fontWeight: FontWeight.w800,
                          color: AppColors.textPrimary,
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                      decoration: BoxDecoration(
                        color: AppColors.tealGreen,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        _formatPrice(price),
                        style: GoogleFonts.nunito(
                          fontSize: 14,
                          fontWeight: FontWeight.w700,
                          color: Colors.white,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 6),
                // Location
                Row(
                  children: [
                    Icon(Icons.location_on, size: 16, color: AppColors.tealGreen),
                    const SizedBox(width: 4),
                    Expanded(
                      child: Text(
                        [ward, district, region].where((s) => s != null && s.toString().isNotEmpty).join(', '),
                        style: GoogleFonts.nunito(
                          fontSize: 13,
                          color: AppColors.textSecondary,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                // Type + Status badges
                Wrap(
                  spacing: 8,
                  children: [
                    _buildBadge(Icons.home_outlined, _capitalize(propertyType), AppColors.tealGreen50, AppColors.tealGreen),
                    if (listingType == 'multi_unit')
                      _buildBadge(Icons.layers_outlined, 'Multi Unit', AppColors.tealGreen50, AppColors.tealGreen),
                    _buildBadge(
                      isAvailable ? Icons.check_circle : Icons.cancel,
                      isAvailable ? 'Available' : 'Occupied',
                      isAvailable ? const Color(0xFFe8f5e9) : const Color(0xFFffebee),
                      isAvailable ? const Color(0xFF2e7d32) : const Color(0xFFc62828),
                    ),
                    if (isFurnished)
                      _buildBadge(Icons.chair, 'Furnished', AppColors.tealGreen50, AppColors.tealGreen),
                  ],
                ),
                const SizedBox(height: 20),

                // Action button
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton.icon(
                    onPressed: () {
                      _showContactDialog(context, contactPhone, ownerName);
                    },
                    icon: const Icon(Icons.phone, size: 18),
                    label: const Text('Get Contact'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.tealGreen,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                  ),
                ),
                const SizedBox(height: 24),

                // Description
                if (description.isNotEmpty) ...[
                  Text(
                    'Description',
                    style: GoogleFonts.nunito(
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    description,
                    style: GoogleFonts.nunito(
                      fontSize: 14,
                      color: AppColors.textSecondary,
                      height: 1.6,
                    ),
                  ),
                  const SizedBox(height: 24),
                ],

                // Property Details
                Text(
                  'Property Details',
                  style: GoogleFonts.nunito(
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textPrimary,
                  ),
                ),
                const SizedBox(height: 12),
                GridView.count(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  crossAxisCount: 3,
                  mainAxisSpacing: 12,
                  crossAxisSpacing: 12,
                  childAspectRatio: 1.0,
                  children: [
                    _buildDetailCard(Icons.bed_outlined, 'Bedrooms', bedrooms?.toString() ?? '0'),
                    _buildDetailCard(Icons.bathroom_outlined, 'Bathrooms', bathrooms?.toString() ?? '0'),
                    _buildDetailCard(Icons.square_foot, 'Area', '${areaSqm ?? 0} m²'),
                  ],
                ),
                const SizedBox(height: 24),

                // Amenities
                if (amenities.isNotEmpty) ...[
                  Text(
                    'Amenities & Features',
                    style: GoogleFonts.nunito(
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary,
                    ),
                  ),
                  const SizedBox(height: 12),
                  Wrap(
                    spacing: 10,
                    runSpacing: 10,
                    children: amenities.map<Widget>((a) {
                      final amenity = a.toString();
                      return Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                        decoration: BoxDecoration(
                          color: AppColors.tealGreen50,
                          borderRadius: BorderRadius.circular(10),
                          border: Border.all(color: AppColors.tealGreen100, width: 1),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(_getAmenityIcon(amenity), size: 16, color: AppColors.tealGreen),
                            const SizedBox(width: 6),
                            Text(
                              _capitalize(amenity),
                              style: GoogleFonts.nunito(
                                fontSize: 12,
                                fontWeight: FontWeight.w600,
                                color: AppColors.textPrimary,
                              ),
                            ),
                          ],
                        ),
                      );
                    }).toList(),
                  ),
                  const SizedBox(height: 24),
                ],

                // Units (if multi_unit)
                if (listingType == 'multi_unit') ...[
                  Text(
                    'Available Units',
                    style: GoogleFonts.nunito(
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary,
                    ),
                  ),
                  const SizedBox(height: 12),
                  ...(_property!['units'] as List<dynamic>? ?? []).map((u) {
                    final unit = u as Map<String, dynamic>;
                    return Container(
                      margin: const EdgeInsets.only(bottom: 10),
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: AppColors.tealGreen100),
                      ),
                      child: Row(
                        children: [
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  unit['unit_name'] ?? 'Unit',
                                  style: GoogleFonts.nunito(
                                    fontSize: 14,
                                    fontWeight: FontWeight.w700,
                                    color: AppColors.textPrimary,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  '${unit['bedrooms'] ?? 0} bed • ${unit['bathrooms'] ?? 0} bath',
                                  style: GoogleFonts.nunito(
                                    fontSize: 12,
                                    color: AppColors.textSecondary,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          Text(
                            _formatPrice(unit['price']),
                            style: GoogleFonts.nunito(
                              fontSize: 13,
                              fontWeight: FontWeight.w700,
                              color: AppColors.tealGreen,
                            ),
                          ),
                        ],
                      ),
                    );
                  }),
                  const SizedBox(height: 24),
                ],

                const SizedBox(height: 80),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildBadge(IconData icon, String label, Color bg, Color fg) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(8),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 14, color: fg),
          const SizedBox(width: 4),
          Text(
            label,
            style: GoogleFonts.nunito(
              fontSize: 11,
              fontWeight: FontWeight.w600,
              color: fg,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDetailCard(IconData icon, String label, String value) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.tealGreen100),
      ),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(icon, size: 24, color: AppColors.tealGreen),
          const SizedBox(height: 6),
          Text(
            value,
            style: GoogleFonts.nunito(
              fontSize: 16,
              fontWeight: FontWeight.w800,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            label,
            style: GoogleFonts.nunito(
              fontSize: 11,
              color: AppColors.textHint,
            ),
          ),
        ],
      ),
    );
  }

  String _capitalize(String s) {
    if (s.isEmpty) return s;
    return s[0].toUpperCase() + s.substring(1).replaceAll('_', ' ');
  }

  void _showContactDialog(BuildContext context, String phone, String owner) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Text('Contact Owner', style: GoogleFonts.nunito(fontWeight: FontWeight.w700)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Owner: $owner', style: GoogleFonts.nunito(fontSize: 14, color: AppColors.textSecondary)),
            const SizedBox(height: 8),
            if (phone.isNotEmpty)
              Text('Phone: $phone', style: GoogleFonts.nunito(fontSize: 14, fontWeight: FontWeight.w600, color: AppColors.tealGreen))
            else
              Text('No contact number available', style: GoogleFonts.nunito(fontSize: 14, color: AppColors.textHint)),
          ],
        ),
        actions: [
          if (phone.isNotEmpty)
            TextButton(
              onPressed: () {
                // TODO: Launch phone dialer
                Navigator.pop(ctx);
              },
              child: const Text('Call Now'),
            ),
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Close')),
        ],
      ),
    );
  }

  void _showAvailabilityDialog(BuildContext context, bool isAvailable) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Text('Availability', style: GoogleFonts.nunito(fontWeight: FontWeight.w700)),
        content: Row(
          children: [
            Icon(
              isAvailable ? Icons.check_circle : Icons.cancel,
              color: isAvailable ? const Color(0xFF2e7d32) : const Color(0xFFc62828),
              size: 28,
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                isAvailable ? 'This property is currently available.' : 'This property is currently occupied.',
                style: GoogleFonts.nunito(fontSize: 14, color: AppColors.textSecondary),
              ),
            ),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('OK')),
        ],
      ),
    );
  }
}

class _VideoPlayerScreen extends StatefulWidget {
  final String videoUrl;
  const _VideoPlayerScreen({required this.videoUrl});

  @override
  State<_VideoPlayerScreen> createState() => _VideoPlayerScreenState();
}

class _VideoPlayerScreenState extends State<_VideoPlayerScreen> {
  late final VideoPlayerController _controller;
  bool _isInitialized = false;
  bool _hasError = false;

  @override
  void initState() {
    super.initState();
    _controller = VideoPlayerController.networkUrl(Uri.parse(widget.videoUrl));
    _controller.initialize().then((_) {
      setState(() => _isInitialized = true);
      _controller.play();
    }).catchError((_) {
      setState(() => _hasError = true);
    });
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.white),
        title: Text('Video Tour', style: GoogleFonts.nunito(color: Colors.white)),
      ),
      body: _hasError
          ? Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.error_outline, color: Colors.white54, size: 48),
                  const SizedBox(height: 12),
                  Text('Unable to play video', style: GoogleFonts.nunito(color: Colors.white54)),
                  const SizedBox(height: 8),
                  TextButton(
                    onPressed: () async {
                      await launchUrl(Uri.parse(widget.videoUrl));
                    },
                    child: Text('Open in browser', style: GoogleFonts.nunito(color: AppColors.tealGreen)),
                  ),
                ],
              ),
            )
          : !_isInitialized
              ? const Center(child: CircularProgressIndicator(color: Colors.white))
              : GestureDetector(
                  onTap: () {
                    setState(() {
                      if (_controller.value.isPlaying) {
                        _controller.pause();
                      } else {
                        _controller.play();
                      }
                    });
                  },
                  child: Center(
                    child: AspectRatio(
                      aspectRatio: _controller.value.aspectRatio,
                      child: Stack(
                        alignment: Alignment.bottomCenter,
                        children: [
                          VideoPlayer(_controller),
                          if (!_controller.value.isPlaying)
                            const Center(
                              child: Icon(Icons.play_arrow, color: Colors.white70, size: 64),
                            ),
                          VideoProgressIndicator(
                            _controller,
                            allowScrubbing: true,
                            colors: const VideoProgressColors(
                              playedColor: Color(0xFF00A86B),
                              bufferedColor: Colors.white24,
                              backgroundColor: Colors.white12,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
    );
  }
}
