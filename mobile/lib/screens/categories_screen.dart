import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../constants/colors.dart';
import '../services/api_service.dart';
import 'category_properties_screen.dart';

class CategoriesScreen extends StatefulWidget {
  const CategoriesScreen({super.key});

  @override
  State<CategoriesScreen> createState() => _CategoriesScreenState();
}

class _CategoriesScreenState extends State<CategoriesScreen> {
  List<Map<String, dynamic>> _categories = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchCategories();
  }

  Future<void> _fetchCategories() async {
    try {
      final data = await ApiService().get('categories');
      final cats = (data['data'] as List<dynamic>?) ?? [];
      setState(() {
        _categories = cats.cast<Map<String, dynamic>>();
        _isLoading = false;
      });
    } catch (_) {
      setState(() => _isLoading = false);
    }
  }

  IconData _getIcon(String? iconName) {
    switch (iconName) {
      case 'home_work': return Icons.home_work_outlined;
      case 'apartment': return Icons.apartment_outlined;
      case 'bed': return Icons.bed_outlined;
      case 'store': return Icons.store_outlined;
      case 'business': return Icons.business_outlined;
      case 'landscape': return Icons.landscape_outlined;
      case 'house': return Icons.house_outlined;
      case 'villa': return Icons.villa_outlined;
      case 'warehouse': return Icons.warehouse_outlined;
      case 'garage': return Icons.garage_outlined;
      case 'farm': return Icons.yard_outlined;
      case 'plot': return Icons.landscape_outlined;
      default: return Icons.category_outlined;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Categories'),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _categories.isEmpty
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.category_outlined, size: 48, color: AppColors.textHint),
                      const SizedBox(height: 12),
                      Text('No categories available', style: GoogleFonts.nunito(color: AppColors.textHint)),
                    ],
                  ),
                )
              : ListView.separated(
                  padding: const EdgeInsets.all(16),
                  itemCount: _categories.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 10),
                  itemBuilder: (context, index) {
                    final cat = _categories[index];
                    final icon = _getIcon(cat['icon'] as String?);
                    final image = cat['image'] as String?;
                    return GestureDetector(
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => CategoryPropertiesScreen(
                              categoryId: cat['id'] as int,
                              categoryName: cat['name'] as String? ?? '',
                            ),
                          ),
                        );
                      },
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(14),
                          border: Border.all(color: AppColors.tealGreen100, width: 1),
                        ),
                        child: Row(
                          children: [
                            if (image != null && image.isNotEmpty)
                              ClipRRect(
                                borderRadius: BorderRadius.circular(10),
                                child: Image.network(
                                  image,
                                  width: 40,
                                  height: 40,
                                  fit: BoxFit.cover,
                                  errorBuilder: (_, __, ___) => Icon(icon, size: 24, color: AppColors.tealGreen),
                                ),
                              )
                            else
                              Container(
                                width: 40,
                                height: 40,
                                decoration: BoxDecoration(
                                  color: AppColors.tealGreen50,
                                  borderRadius: BorderRadius.circular(10),
                                ),
                                child: Icon(icon, size: 24, color: AppColors.tealGreen),
                              ),
                            const SizedBox(width: 14),
                            Expanded(
                              child: Text(
                                cat['name'] as String? ?? '',
                                style: GoogleFonts.nunito(
                                  fontSize: 15,
                                  fontWeight: FontWeight.w700,
                                  color: AppColors.textPrimary,
                                ),
                              ),
                            ),
                            Icon(Icons.chevron_right, size: 22, color: AppColors.textHint),
                          ],
                        ),
                      ),
                    );
                  },
                ),
    );
  }
}
