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
  bool _isFavorited = false;
  bool _isFavLoading = false;

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
        _checkFavorite();
      }
    } catch (_) {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _checkFavorite() async {
    try {
      final data = await ApiService().get('favorites/check/${widget.propertyId}');
      setState(() => _isFavorited = data['is_favorited'] == true);
    } catch (_) {}
  }

  Future<void> _toggleFavorite() async {
    if (_isFavLoading) return;
    setState(() => _isFavLoading = true);
    final wasFavorited = _isFavorited;
    setState(() => _isFavorited = !wasFavorited);
    try {
      await ApiService().post('favorites/toggle', body: {'property_id': widget.propertyId});
    } catch (_) {
      setState(() => _isFavorited = wasFavorited);
    }
    setState(() => _isFavLoading = false);
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
            Padding(
              padding: const EdgeInsets.all(8),
              child: CircleAvatar(
                backgroundColor: Colors.black.withValues(alpha: 0.4),
                child: IconButton(
                  icon: Icon(
                    _isFavorited ? Icons.favorite : Icons.favorite_border,
                    color: _isFavorited ? Colors.redAccent : Colors.white,
                    size: 20,
                  ),
                  onPressed: _toggleFavorite,
                ),
              ),
            ),
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
                const SizedBox(height: 10),
                Wrap(
                  spacing: 10,
                  runSpacing: 10,
                  children: [
                    _buildDetailChip(Icons.bed_outlined, 'Bedrooms', bedrooms?.toString() ?? '0'),
                    _buildDetailChip(Icons.bathroom_outlined, 'Bathrooms', bathrooms?.toString() ?? '0'),
                    _buildDetailChip(Icons.square_foot, 'Area', '${areaSqm ?? 0} m²'),
                    _buildDetailChip(Icons.home_outlined, 'Type', _capitalize(propertyType)),
                    _buildDetailChip(Icons.attach_money, 'Period', _capitalize(rentalPeriod)),
                    _buildDetailChip(Icons.layers_outlined, 'Listing', listingType == 'multi_unit' ? 'Multi Unit' : 'Single'),
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

                // Recommended Properties
                if (_recommended.isNotEmpty) ...[
                  const SizedBox(height: 8),
                  Text(
                    'Recommended Properties',
                    style: GoogleFonts.nunito(
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary,
                    ),
                  ),
                  const SizedBox(height: 12),
                  SizedBox(
                    height: 200,
                    child: ListView.builder(
                      scrollDirection: Axis.horizontal,
                      itemCount: _recommended.length,
                      itemBuilder: (context, index) {
                        final p = _recommended[index];
                        final imgs = p['images'] as List<dynamic>?;
                        String? imgUrl;
                        if (imgs != null && imgs.isNotEmpty) {
                          final img = imgs[0] as Map<String, dynamic>;
                          imgUrl = img['url'] ?? img['image_url'];
                        }
                        final pTitle = p['title'] ?? 'Untitled';
                        final pLocation = '${p['region'] ?? ''}, ${p['district'] ?? ''}';
                        final pPrice = p['price'];
                        return GestureDetector(
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (_) => PropertyDetailScreen(propertyId: p['id'] as int),
                              ),
                            );
                          },
                          child: Container(
                            width: 160,
                            margin: const EdgeInsets.only(right: 12),
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(color: AppColors.tealGreen100),
                            ),
                            child: ClipRRect(
                              borderRadius: BorderRadius.circular(12),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Expanded(
                                    flex: 5,
                                    child: Container(
                                      width: double.infinity,
                                      color: AppColors.tealGreen50,
                                      child: imgUrl != null && imgUrl.isNotEmpty
                                          ? Image.network(
                                              imgUrl,
                                              fit: BoxFit.cover,
                                              errorBuilder: (_, __, ___) => Center(
                                                child: Icon(Icons.home_outlined, size: 28, color: AppColors.tealGreen),
                                              ),
                                            )
                                          : Center(
                                              child: Icon(Icons.home_outlined, size: 28, color: AppColors.tealGreen),
                                            ),
                                    ),
                                  ),
                                  Expanded(
                                    flex: 4,
                                    child: Padding(
                                      padding: const EdgeInsets.all(8),
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            pTitle,
                                            maxLines: 1,
                                            overflow: TextOverflow.ellipsis,
                                            style: GoogleFonts.nunito(
                                              fontSize: 12,
                                              fontWeight: FontWeight.w700,
                                              color: AppColors.textPrimary,
                                            ),
                                          ),
                                          const SizedBox(height: 2),
                                          Row(
                                            children: [
                                              Icon(Icons.location_on, size: 10, color: AppColors.textHint),
                                              const SizedBox(width: 2),
                                              Expanded(
                                                child: Text(
                                                  pLocation,
                                                  maxLines: 1,
                                                  overflow: TextOverflow.ellipsis,
                                                  style: GoogleFonts.nunito(
                                                    fontSize: 10,
                                                    color: AppColors.textSecondary,
                                                  ),
                                                ),
                                              ),
                                            ],
                                          ),
                                          const Spacer(),
                                          if (pPrice != null)
                                            Text(
                                              _formatPrice(pPrice),
                                              style: GoogleFonts.nunito(
                                                fontSize: 11,
                                                fontWeight: FontWeight.w700,
                                                color: AppColors.tealGreen,
                                              ),
                                            ),
                                        ],
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        );
                      },
                    ),
                  ),
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

  Widget _buildDetailChip(IconData icon, String label, String value) {
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
          Icon(icon, size: 16, color: AppColors.tealGreen),
          const SizedBox(width: 6),
          Text(
            '$label: ',
            style: GoogleFonts.nunito(
              fontSize: 12,
              fontWeight: FontWeight.w500,
              color: AppColors.textSecondary,
            ),
          ),
          Text(
            value,
            style: GoogleFonts.nunito(
              fontSize: 12,
              fontWeight: FontWeight.w700,
              color: AppColors.textPrimary,
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
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      isScrollControlled: true,
      builder: (ctx) => Container(
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
        ),
        child: Padding(
          padding: const EdgeInsets.fromLTRB(20, 12, 20, 34),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: Colors.grey[300],
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
              const SizedBox(height: 20),
              CircleAvatar(
                radius: 32,
                backgroundColor: AppColors.tealGreen50,
                child: Icon(Icons.person, size: 36, color: AppColors.tealGreen),
              ),
              const SizedBox(height: 12),
              Text(
                owner,
                style: GoogleFonts.nunito(
                  fontSize: 18,
                  fontWeight: FontWeight.w800,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 4),
              if (phone.isNotEmpty)
                Text(
                  phone,
                  style: GoogleFonts.nunito(
                    fontSize: 14,
                    color: AppColors.textSecondary,
                  ),
                )
              else
                Text(
                  'No contact number available',
                  style: GoogleFonts.nunito(
                    fontSize: 14,
                    color: AppColors.textHint,
                  ),
                ),
              const SizedBox(height: 24),
              if (phone.isNotEmpty) ...[
                _buildCallListItem(
                  icon: Icons.phone_in_talk,
                  title: 'Call Online',
                  subtitle: 'Call through the app (logged)',
                  color: AppColors.tealGreen,
                  onTap: () {
                    Navigator.pop(context);
                    _startOnlineCall(context, phone, owner);
                  },
                ),
                const SizedBox(height: 10),
                _buildCallListItem(
                  icon: Icons.dialer_sip,
                  title: 'Call Offline',
                  subtitle: 'Use phone dialer',
                  color: const Color(0xFF2196F3),
                  onTap: () {
                    Navigator.pop(context);
                    _startOfflineCall(context, phone);
                  },
                ),
                const SizedBox(height: 16),
                Text(
                  'Online calls are logged in the system. Offline calls use your phone\'s dialer.',
                  textAlign: TextAlign.center,
                  style: GoogleFonts.nunito(
                    fontSize: 11,
                    color: AppColors.textHint,
                  ),
                ),
              ],
              const SizedBox(height: 12),
              TextButton(
                onPressed: () => Navigator.pop(context),
                child: Text(
                  'Cancel',
                  style: GoogleFonts.nunito(
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                    color: AppColors.textSecondary,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildCallListItem({
    required IconData icon,
    required String title,
    required String subtitle,
    required Color color,
    required VoidCallback onTap,
  }) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(14),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 16),
          decoration: BoxDecoration(
            color: color.withValues(alpha: 0.06),
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: color.withValues(alpha: 0.15), width: 1),
          ),
          child: Row(
            children: [
              Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  color: color,
                  shape: BoxShape.circle,
                ),
                child: Icon(icon, color: Colors.white, size: 22),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: GoogleFonts.nunito(
                        fontSize: 14,
                        fontWeight: FontWeight.w800,
                        color: AppColors.textPrimary,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      subtitle,
                      style: GoogleFonts.nunito(
                        fontSize: 11,
                        color: AppColors.textHint,
                      ),
                    ),
                  ],
                ),
              ),
              Icon(Icons.chevron_right, color: color, size: 24),
            ],
          ),
        ),
      ),
    );
  }

  void _startOnlineCall(BuildContext context, String phone, String owner) {
    _logCall('online', phone);
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => _InAppCallScreen(
          phone: phone,
          ownerName: owner,
          propertyId: widget.propertyId,
        ),
      ),
    );
  }

  void _startOfflineCall(BuildContext context, String phone) async {
    _logCall('offline', phone);
    final uri = Uri.parse('tel:$phone');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  Future<void> _logCall(String callType, String phone) async {
    try {
      await ApiService().post(
        'properties/${widget.propertyId}/call',
        body: {'call_type': callType, 'contact_phone': phone},
      );
    } catch (_) {}
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

class _InAppCallScreen extends StatefulWidget {
  final String phone;
  final String ownerName;
  final int propertyId;

  const _InAppCallScreen({
    required this.phone,
    required this.ownerName,
    required this.propertyId,
  });

  @override
  State<_InAppCallScreen> createState() => _InAppCallScreenState();
}

class _InAppCallScreenState extends State<_InAppCallScreen> with TickerProviderStateMixin {
  late AnimationController _pulseController;
  late Animation<double> _pulseAnimation;
  int _callSeconds = 0;
  bool _isMuted = false;
  bool _isSpeakerOn = true;
  bool _isConnecting = true;
  bool _callEnded = false;

  @override
  void initState() {
    super.initState();
    _pulseController = AnimationController(
      duration: const Duration(milliseconds: 1500),
      vsync: this,
    )..repeat(reverse: true);
    _pulseAnimation = Tween<double>(begin: 0.8, end: 1.2).animate(
      CurvedAnimation(parent: _pulseController, curve: Curves.easeInOut),
    );

    Future.delayed(const Duration(seconds: 3), () {
      if (mounted) {
        setState(() => _isConnecting = false);
      }
    });
  }

  @override
  void dispose() {
    _pulseController.dispose();
    super.dispose();
  }

  void _endCall() {
    setState(() => _callEnded = true);
    Future.delayed(const Duration(milliseconds: 500), () {
      if (mounted) Navigator.pop(context);
    });
  }

  String _formatDuration(int seconds) {
    final m = seconds ~/ 60;
    final s = seconds % 60;
    return '${m.toString().padLeft(2, '0')}:${s.toString().padLeft(2, '0')}';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0D1B2A),
      body: SafeArea(
        child: Column(
          children: [
            const SizedBox(height: 40),
            Text(
              _callEnded ? 'Call Ended' : 'PataNyumba Call',
              style: GoogleFonts.nunito(
                fontSize: 14,
                color: Colors.white54,
                fontWeight: FontWeight.w600,
              ),
            ),
            const SizedBox(height: 60),
            AnimatedBuilder(
              animation: _pulseAnimation,
              builder: (context, child) {
                return Transform.scale(
                  scale: _isConnecting ? _pulseAnimation.value : 1.0,
                  child: Container(
                    width: 120,
                    height: 120,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      gradient: LinearGradient(
                        colors: [
                          AppColors.tealGreen.withValues(alpha: 0.3),
                          AppColors.tealGreen,
                        ],
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: AppColors.tealGreen.withValues(alpha: 0.3),
                          blurRadius: 30,
                          spreadRadius: 5,
                        ),
                      ],
                    ),
                    child: const Icon(Icons.person, size: 56, color: Colors.white),
                  ),
                );
              },
            ),
            const SizedBox(height: 24),
            Text(
              widget.ownerName,
              style: GoogleFonts.nunito(
                fontSize: 24,
                fontWeight: FontWeight.w800,
                color: Colors.white,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              widget.phone,
              style: GoogleFonts.nunito(
                fontSize: 16,
                color: Colors.white54,
              ),
            ),
            const SizedBox(height: 16),
            if (_callEnded)
              Text(
                _formatDuration(_callSeconds),
                style: GoogleFonts.nunito(
                  fontSize: 20,
                  fontWeight: FontWeight.w700,
                  color: Colors.redAccent,
                ),
              )
            else if (_isConnecting)
              Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  SizedBox(
                    width: 16,
                    height: 16,
                    child: CircularProgressIndicator(
                      strokeWidth: 2,
                      color: AppColors.tealGreen.withValues(alpha: 0.7),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Text(
                    'Connecting...',
                    style: GoogleFonts.nunito(
                      fontSize: 16,
                      color: Colors.white54,
                    ),
                  ),
                ],
              )
            else
              StreamBuilder<int>(
                stream: Stream.periodic(const Duration(seconds: 1), (i) => i + 1),
                builder: (context, snapshot) {
                  if (snapshot.hasData) {
                    _callSeconds = snapshot.data!;
                  }
                  return Text(
                    _formatDuration(_callSeconds),
                    style: GoogleFonts.nunito(
                      fontSize: 28,
                      fontWeight: FontWeight.w300,
                      color: Colors.white,
                    ),
                  );
                },
              ),
            const Spacer(),
            if (!_isConnecting && !_callEnded) ...[
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 40),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                  children: [
                    _buildCallControlButton(
                      icon: _isMuted ? Icons.mic_off : Icons.mic,
                      label: 'Mute',
                      isActive: _isMuted,
                      onTap: () => setState(() => _isMuted = !_isMuted),
                    ),
                    _buildCallControlButton(
                      icon: _isSpeakerOn ? Icons.volume_up : Icons.volume_off,
                      label: 'Speaker',
                      isActive: _isSpeakerOn,
                      onTap: () => setState(() => _isSpeakerOn = !_isSpeakerOn),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 30),
            ],
            Padding(
              padding: const EdgeInsets.only(bottom: 50),
              child: GestureDetector(
                onTap: _endCall,
                child: Container(
                  width: 70,
                  height: 70,
                  decoration: const BoxDecoration(
                    color: Colors.red,
                    shape: BoxShape.circle,
                    boxShadow: [
                      BoxShadow(
                        color: Colors.redAccent,
                        blurRadius: 20,
                        spreadRadius: 2,
                      ),
                    ],
                  ),
                  child: const Icon(Icons.call_end, color: Colors.white, size: 32),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildCallControlButton({
    required IconData icon,
    required String label,
    required bool isActive,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Column(
        children: [
          Container(
            width: 56,
            height: 56,
            decoration: BoxDecoration(
              color: isActive
                  ? AppColors.tealGreen.withValues(alpha: 0.3)
                  : Colors.white.withValues(alpha: 0.1),
              shape: BoxShape.circle,
              border: Border.all(
                color: isActive ? AppColors.tealGreen : Colors.white24,
                width: 1.5,
              ),
            ),
            child: Icon(icon, color: Colors.white, size: 24),
          ),
          const SizedBox(height: 6),
          Text(
            label,
            style: GoogleFonts.nunito(
              fontSize: 11,
              color: Colors.white54,
            ),
          ),
        ],
      ),
    );
  }
}
