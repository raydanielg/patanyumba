import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../constants/colors.dart';
import '../services/api_service.dart';
import 'property_detail_screen.dart';

class CategoryPropertiesScreen extends StatefulWidget {
  final int categoryId;
  final String categoryName;

  const CategoryPropertiesScreen({
    super.key,
    required this.categoryId,
    required this.categoryName,
  });

  @override
  State<CategoryPropertiesScreen> createState() => _CategoryPropertiesScreenState();
}

class _CategoryPropertiesScreenState extends State<CategoryPropertiesScreen> {
  List<Map<String, dynamic>> _properties = [];
  List<String> _regions = [];
  String? _selectedRegion;
  bool _isLoading = true;
  bool _isFilterOpen = false;

  @override
  void initState() {
    super.initState();
    _fetchProperties();
    _fetchRegions();
  }

  Future<void> _fetchProperties() async {
    setState(() => _isLoading = true);
    try {
      String endpoint = 'properties?category_id=${widget.categoryId}';
      if (_selectedRegion != null) {
        endpoint += '&region=${Uri.encodeComponent(_selectedRegion!)}';
      }
      final data = await ApiService().get(endpoint);
      final raw = data['data'];
      final props = (raw is List<dynamic>) ? raw : (raw['data'] as List<dynamic>?) ?? [];
      setState(() {
        _properties = props.cast<Map<String, dynamic>>();
        _isLoading = false;
      });
    } catch (_) {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _fetchRegions() async {
    try {
      final data = await ApiService().get('regions');
      final regs = (data['data'] as List<dynamic>?) ?? [];
      setState(() {
        _regions = regs.cast<String>();
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.categoryName),
        actions: [
          IconButton(
            icon: Icon(_isFilterOpen ? Icons.filter_list_off : Icons.filter_list, size: 22),
            onPressed: () => setState(() => _isFilterOpen = !_isFilterOpen),
          ),
        ],
      ),
      body: Column(
        children: [
          // Region filter chips
          if (_isFilterOpen)
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              color: Colors.white,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Filter by Region',
                    style: GoogleFonts.nunito(
                      fontSize: 13,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary,
                    ),
                  ),
                  const SizedBox(height: 10),
              Wrap(
                spacing: 8,
                runSpacing: 8,
                children: [
                  FilterChip(
                    label: const Text('All'),
                    selected: _selectedRegion == null,
                    onSelected: (_) {
                      setState(() => _selectedRegion = null);
                      _fetchProperties();
                    },
                    selectedColor: AppColors.tealGreen,
                    labelStyle: TextStyle(
                      color: _selectedRegion == null ? Colors.white : AppColors.textPrimary,
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                    ),
                    backgroundColor: AppColors.tealGreen50,
                    side: BorderSide(color: AppColors.tealGreen100),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                  ),
                  ..._regions.map((region) {
                    final selected = _selectedRegion == region;
                    return FilterChip(
                      label: Text(region),
                      selected: selected,
                      onSelected: (_) {
                        setState(() => _selectedRegion = selected ? null : region);
                        _fetchProperties();
                      },
                      selectedColor: AppColors.tealGreen,
                      labelStyle: TextStyle(
                        color: selected ? Colors.white : AppColors.textPrimary,
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                      ),
                      backgroundColor: AppColors.tealGreen50,
                      side: BorderSide(color: AppColors.tealGreen100),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                    );
                  }),
                ],
              ),
                ],
              ),
            ),
          if (_isFilterOpen) const Divider(height: 1, color: AppColors.tealGreen100),
          // Properties list
          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator())
                : _properties.isEmpty
                    ? Center(
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(Icons.home_outlined, size: 48, color: AppColors.textHint),
                            const SizedBox(height: 12),
                            Text(
                              'No properties in this category',
                              style: GoogleFonts.nunito(color: AppColors.textHint, fontSize: 14),
                            ),
                            if (_selectedRegion != null) ...[
                              const SizedBox(height: 4),
                              Text(
                                'in $_selectedRegion',
                                style: GoogleFonts.nunito(color: AppColors.textHint, fontSize: 12),
                              ),
                            ],
                          ],
                        ),
                      )
                    : ListView.builder(
                        padding: const EdgeInsets.all(16),
                        itemCount: _properties.length,
                        itemBuilder: (context, index) {
                          final p = _properties[index];
                          final images = p['images'] as List<dynamic>?;
                          final imageUrl = images != null && images.isNotEmpty
                              ? images[0]['url'] ?? images[0]['image_url']
                              : null;
                          final propertyId = p['id'] as int;
                          return GestureDetector(
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (_) => PropertyDetailScreen(propertyId: propertyId),
                                ),
                              );
                            },
                            child: Container(
                              margin: const EdgeInsets.only(bottom: 12),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(14),
                                boxShadow: [
                                  BoxShadow(
                                    color: Colors.black.withValues(alpha: 0.05),
                                    blurRadius: 8,
                                    offset: const Offset(0, 2),
                                  ),
                                ],
                              ),
                              child: Row(
                                children: [
                                  ClipRRect(
                                    borderRadius: const BorderRadius.horizontal(left: Radius.circular(14)),
                                    child: imageUrl != null
                                        ? Image.network(
                                            imageUrl,
                                            width: 100,
                                            height: 100,
                                            fit: BoxFit.cover,
                                            errorBuilder: (_, __, ___) => Container(
                                              width: 100,
                                              height: 100,
                                              color: AppColors.tealGreen50,
                                              child: Icon(Icons.home_outlined, color: AppColors.tealGreen),
                                            ),
                                          )
                                        : Container(
                                            width: 100,
                                            height: 100,
                                            color: AppColors.tealGreen50,
                                            child: Icon(Icons.home_outlined, color: AppColors.tealGreen),
                                          ),
                                  ),
                                  Expanded(
                                    child: Padding(
                                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            p['title'] ?? 'Untitled',
                                            maxLines: 1,
                                            overflow: TextOverflow.ellipsis,
                                            style: GoogleFonts.nunito(
                                              fontSize: 14,
                                              fontWeight: FontWeight.w700,
                                              color: AppColors.textPrimary,
                                            ),
                                          ),
                                          const SizedBox(height: 4),
                                          Row(
                                            children: [
                                              Icon(Icons.location_on_outlined, size: 14, color: AppColors.textHint),
                                              const SizedBox(width: 3),
                                              Expanded(
                                                child: Text(
                                                  '${p['region'] ?? ''}, ${p['district'] ?? ''}',
                                                  maxLines: 1,
                                                  overflow: TextOverflow.ellipsis,
                                                  style: GoogleFonts.nunito(
                                                    fontSize: 12,
                                                    color: AppColors.textSecondary,
                                                  ),
                                                ),
                                              ),
                                            ],
                                          ),
                                          const SizedBox(height: 6),
                                          Container(
                                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                            decoration: BoxDecoration(
                                              color: AppColors.tealGreen50,
                                              borderRadius: BorderRadius.circular(6),
                                            ),
                                            child: Text(
                                              _formatPrice(p['price']),
                                              style: GoogleFonts.nunito(
                                                fontSize: 12,
                                                fontWeight: FontWeight.w700,
                                                color: AppColors.tealGreen,
                                              ),
                                            ),
                                          ),
                                        ],
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          );
                        },
                      ),
          ),
        ],
      ),
    );
  }
}
