import 'dart:async';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../constants/colors.dart';
import '../constants/constants.dart';
import '../services/api_service.dart';
import '../services/auth_service.dart';
import 'categories_screen.dart';
import 'category_properties_screen.dart';
import 'notifications_screen.dart';
import 'property_detail_screen.dart';
import 'settings_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int _currentIndex = 0;

  final List<Widget> _pages = [
    const _HomePage(),
    const _SearchPage(),
    const _FavoritesPage(),
    const _ProfilePage(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: _pages[_currentIndex],
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _currentIndex,
        onTap: (index) => setState(() => _currentIndex = index),
        items: const [
          BottomNavigationBarItem(
            icon: Icon(Icons.home_outlined),
            activeIcon: Icon(Icons.home),
            label: 'Home',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.search_outlined),
            activeIcon: Icon(Icons.search),
            label: 'Search',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.favorite_outline),
            activeIcon: Icon(Icons.favorite),
            label: 'Saved',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.person_outline),
            activeIcon: Icon(Icons.person),
            label: 'Profile',
          ),
        ],
      ),
    );
  }
}

class _HomePage extends StatefulWidget {
  const _HomePage();

  @override
  State<_HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<_HomePage> {
  final PageController _heroController = PageController();
  int _currentHeroPage = 0;
  Timer? _heroTimer;
  List<Map<String, dynamic>> _heroSlides = [];
  bool _heroEnabled = true;
  List<Map<String, dynamic>> _categories = [];
  List<Map<String, dynamic>> _featuredProperties = [];

  @override
  void initState() {
    super.initState();
    _fetchHeroSlides();
    _fetchCategories();
    _fetchFeaturedProperties();
    _heroTimer = Timer.periodic(const Duration(seconds: 4), (_) {
      if (_heroController.hasClients && _heroSlides.length > 1) {
        int next = (_currentHeroPage + 1) % _heroSlides.length;
        _heroController.animateToPage(
          next,
          duration: const Duration(milliseconds: 600),
          curve: Curves.easeInOut,
        );
      }
    });
  }

  Future<void> _fetchHeroSlides() async {
    try {
      final data = await ApiService().get('hero-slides');
      final enabled = data['enabled'] as bool? ?? true;
      final slides = (data['data'] as List<dynamic>?) ?? [];
      final castSlides = slides.cast<Map<String, dynamic>>();
      // Only show hero if enabled AND there are slides with images
      final hasRealSlides = castSlides.any((s) => s['image'] != null && (s['image'] as String).isNotEmpty);
      setState(() {
        _heroEnabled = enabled && hasRealSlides;
        _heroSlides = hasRealSlides ? castSlides : [];
      });
    } catch (_) {
      setState(() {
        _heroEnabled = false;
        _heroSlides = [];
      });
    }
  }

  Future<void> _fetchCategories() async {
    try {
      final data = await ApiService().get('categories');
      final cats = (data['data'] as List<dynamic>?) ?? [];
      setState(() {
        _categories = cats.cast<Map<String, dynamic>>();
      });
    } catch (_) {}
  }

  Future<void> _fetchFeaturedProperties() async {
    try {
      final data = await ApiService().get('properties?per_page=6');
      final props = (data['data'] as List<dynamic>?) ?? [];
      setState(() {
        _featuredProperties = props.cast<Map<String, dynamic>>();
      });
    } catch (_) {}
  }

  @override
  void dispose() {
    _heroTimer?.cancel();
    _heroController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        titleSpacing: 0,
        title: Padding(
          padding: const EdgeInsets.only(left: 16),
          child: Row(
            children: [
              Image.asset('assets/logo/whitelogo.png', width: 30, height: 30),
              const SizedBox(width: 10),
              const Text(
                AppConstants.appName,
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ],
          ),
        ),
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Hero Carousel (hidden if disabled or no slides with images)
            if (_heroEnabled) ...[
              _buildHeroCarousel(),
              const SizedBox(height: 16),
            ],
            // Search bar
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: GestureDetector(
                onTap: () => _openSearch(context),
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(12),
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
                      Icon(Icons.search, size: 20, color: AppColors.textHint),
                      const SizedBox(width: 10),
                      Expanded(
                        child: Text(
                          'Search for properties, locations...',
                          style: TextStyle(
                            fontSize: 14,
                            color: AppColors.textHint,
                          ),
                        ),
                      ),
                      Icon(Icons.tune, size: 20, color: AppColors.tealGreen),
                    ],
                  ),
                ),
              ),
            ),
            const SizedBox(height: 20),
            // Categories
            if (_categories.isNotEmpty)
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          'Categories',
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.w700,
                            color: AppColors.textPrimary,
                          ),
                        ),
                        GestureDetector(
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(builder: (_) => const CategoriesScreen()),
                            );
                          },
                          child: Row(
                            children: [
                              Text(
                                'See All',
                                style: TextStyle(
                                  fontSize: 13,
                                  fontWeight: FontWeight.w600,
                                  color: AppColors.tealGreen,
                                ),
                              ),
                              const SizedBox(width: 4),
                              Icon(Icons.arrow_forward_ios, size: 10, color: AppColors.tealGreen),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 14),
                    SizedBox(
                      height: 65,
                      child: ListView.builder(
                        scrollDirection: Axis.horizontal,
                        itemCount: _categories.length,
                        itemBuilder: (context, index) {
                          final cat = _categories[index];
                          return _buildCategoryCard(
                            cat['name'] as String? ?? '',
                            cat['image'] as String?,
                            cat['icon'] as String?,
                            cat['id'] as int,
                          );
                        },
                      ),
                    ),
                    const SizedBox(height: 24),
                  ],
                ),
              ),
            // Featured Properties - 2 column grid
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Featured Listings',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary,
                    ),
                  ),
                  const SizedBox(height: 16),
                  if (_featuredProperties.isNotEmpty)
                    GridView.builder(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: 2,
                        mainAxisSpacing: 12,
                        crossAxisSpacing: 12,
                        childAspectRatio: 0.72,
                      ),
                      itemCount: _featuredProperties.length,
                      itemBuilder: (context, index) {
                        final p = _featuredProperties[index];
                        final images = p['images'] as List<dynamic>?;
                        final imageUrl = images != null && images.isNotEmpty
                            ? images[0]['url'] ?? images[0]['image_url']
                            : null;
                        final title = p['title'] ?? 'Untitled';
                        final location = '${p['region'] ?? ''}, ${p['district'] ?? ''}';
                        final price = p['price'];
                        final listingType = p['listing_type'] ?? 'single';
                        return _buildPropertyCard(
                          title,
                          location,
                          imageUrl,
                          price,
                          listingType,
                          p['id'] as int,
                        );
                      },
                    )
                  else
                    SizedBox(
                      height: 200,
                      child: Center(
                        child: Text(
                          'No properties available',
                          style: TextStyle(fontSize: 14, color: AppColors.textHint),
                        ),
                      ),
                    ),
                  const SizedBox(height: 24),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _openSearch(BuildContext context) {
    showGeneralDialog(
      context: context,
      barrierDismissible: true,
      barrierLabel: 'Search',
      barrierColor: Colors.black54,
      transitionDuration: const Duration(milliseconds: 300),
      pageBuilder: (context, animation, secondaryAnimation) {
        return const _SearchPopup();
      },
      transitionBuilder: (context, animation, secondaryAnimation, child) {
        return SlideTransition(
          position: Tween<Offset>(
            begin: const Offset(0, -1),
            end: Offset.zero,
          ).animate(CurvedAnimation(parent: animation, curve: Curves.easeOut)),
          child: child,
        );
      },
    );
  }

  Widget _buildHeroCarousel() {
    return SizedBox(
      height: 220,
      child: Stack(
        children: [
          PageView.builder(
            controller: _heroController,
            itemCount: _heroSlides.length,
            onPageChanged: (index) => setState(() => _currentHeroPage = index),
            itemBuilder: (context, index) {
              final slide = _heroSlides[index];
              final imageUrl = slide['image'] as String?;
              return Container(
                width: double.infinity,
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [
                      AppColors.darkTealGreen,
                      AppColors.tealGreen.withValues(alpha: 0.8),
                    ],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                ),
                child: Stack(
                  children: [
                    // Background image (from API URL or fallback gradient)
                    if (imageUrl != null && imageUrl.isNotEmpty)
                      Positioned.fill(
                        child: Image.network(
                          imageUrl,
                          fit: BoxFit.cover,
                          loadingBuilder: (context, child, progress) {
                            if (progress == null) return child;
                            return Container(
                              decoration: BoxDecoration(
                                gradient: LinearGradient(
                                  colors: [
                                    AppColors.darkTealGreen,
                                    AppColors.tealGreen,
                                  ],
                                  begin: Alignment.topLeft,
                                  end: Alignment.bottomRight,
                                ),
                              ),
                              child: Center(
                                child: CircularProgressIndicator(
                                  color: Colors.white.withValues(alpha: 0.5),
                                  strokeWidth: 2,
                                ),
                              ),
                            );
                          },
                          errorBuilder: (context, error, stackTrace) {
                            return Container(
                              decoration: BoxDecoration(
                                gradient: LinearGradient(
                                  colors: [
                                    AppColors.darkTealGreen,
                                    AppColors.tealGreen,
                                  ],
                                  begin: Alignment.topLeft,
                                  end: Alignment.bottomRight,
                                ),
                              ),
                            );
                          },
                        ),
                      ),
                    // Dark overlay for text readability
                    Positioned.fill(
                      child: Container(
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            colors: [
                              Colors.black.withValues(alpha: 0.5),
                              Colors.black.withValues(alpha: 0.2),
                            ],
                            begin: Alignment.bottomCenter,
                            end: Alignment.topCenter,
                          ),
                        ),
                      ),
                    ),
                    // Text content + CTA button
                    Positioned(
                      left: 20,
                      bottom: 24,
                      right: 20,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Text(
                            slide['title'] as String? ?? 'Find Your Perfect Home',
                            style: const TextStyle(
                              fontSize: 22,
                              fontWeight: FontWeight.w800,
                              color: Colors.white,
                              height: 1.2,
                            ),
                          ),
                          const SizedBox(height: 6),
                          Text(
                            slide['subtitle'] as String? ?? '',
                            style: TextStyle(
                              fontSize: 13,
                              color: Colors.white.withValues(alpha: 0.85),
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                          if ((slide['button_text'] as String? ?? '').isNotEmpty) ...[
                            const SizedBox(height: 12),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(20),
                              ),
                              child: Text(
                                slide['button_text'] as String,
                                style: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w700,
                                  color: AppColors.tealGreen,
                                ),
                              ),
                            ),
                          ],
                        ],
                      ),
                    ),
                  ],
                ),
              );
            },
          ),
          // Dots indicator
          Positioned(
            bottom: 8,
            right: 0,
            left: 0,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: List.generate(
                _heroSlides.length,
                (index) => AnimatedContainer(
                  duration: const Duration(milliseconds: 300),
                  margin: const EdgeInsets.symmetric(horizontal: 3),
                  width: _currentHeroPage == index ? 20 : 6,
                  height: 6,
                  decoration: BoxDecoration(
                    color: _currentHeroPage == index
                        ? Colors.white
                        : Colors.white.withValues(alpha: 0.4),
                    borderRadius: BorderRadius.circular(3),
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCategoryCard(String name, String? imageUrl, String? iconName, int categoryId) {
    final icon = _getCategoryIcon(iconName);
    return GestureDetector(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => CategoryPropertiesScreen(
              categoryId: categoryId,
              categoryName: name,
            ),
          ),
        );
      },
      child: Container(
        width: 64,
        margin: const EdgeInsets.only(right: 10),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            if (imageUrl != null && imageUrl.isNotEmpty)
              ClipRRect(
                borderRadius: BorderRadius.circular(8),
                child: Image.network(
                  imageUrl,
                  width: 32,
                  height: 32,
                  fit: BoxFit.cover,
                  errorBuilder: (_, __, ___) => Icon(icon, size: 22, color: AppColors.tealGreen),
                ),
              )
            else
              Icon(icon, size: 22, color: AppColors.tealGreen),
            const SizedBox(height: 4),
            Text(
              name,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              textAlign: TextAlign.center,
              style: const TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.w500,
                color: AppColors.textPrimary,
              ),
            ),
          ],
        ),
      ),
    );
  }

  IconData _getCategoryIcon(String? iconName) {
    switch (iconName) {
      case 'home_work':
        return Icons.home_work_outlined;
      case 'apartment':
        return Icons.apartment_outlined;
      case 'bed':
        return Icons.bed_outlined;
      case 'store':
        return Icons.store_outlined;
      case 'business':
        return Icons.business_outlined;
      case 'landscape':
        return Icons.landscape_outlined;
      case 'house':
        return Icons.house_outlined;
      case 'villa':
        return Icons.villa_outlined;
      case 'warehouse':
        return Icons.warehouse_outlined;
      case 'garage':
        return Icons.garage_outlined;
      case 'farm':
        return Icons.yard_outlined;
      case 'plot':
        return Icons.landscape_outlined;
      default:
        return Icons.category_outlined;
    }
  }

  Widget _buildPropertyCard(String title, String location, String? imageUrl, dynamic price, String listingType, int propertyId) {
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
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(14),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.06),
              blurRadius: 8,
              offset: const Offset(0, 3),
            ),
          ],
        ),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(14),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Image
              Expanded(
                flex: 5,
                child: Container(
                  width: double.infinity,
                  color: AppColors.tealGreen50,
                  child: Stack(
                    children: [
                      if (imageUrl != null && imageUrl.isNotEmpty)
                        Image.network(
                          imageUrl,
                          fit: BoxFit.cover,
                          width: double.infinity,
                          height: double.infinity,
                          errorBuilder: (_, __, ___) => Center(
                            child: Icon(Icons.home_work_outlined, size: 40, color: AppColors.tealGreen.withValues(alpha: 0.6)),
                          ),
                        )
                      else
                        Center(
                          child: Icon(
                            Icons.home_work_outlined,
                            size: 40,
                            color: AppColors.tealGreen.withValues(alpha: 0.6),
                          ),
                        ),
                      Positioned(
                        top: 8,
                        right: 8,
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                          decoration: BoxDecoration(
                            color: AppColors.lightGreen.withValues(alpha: 0.9),
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Text(
                            listingType == 'multi_unit' ? 'Multi' : 'Rent',
                            style: const TextStyle(
                              fontSize: 9,
                              fontWeight: FontWeight.w700,
                              color: Colors.white,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              // Title + Location + Price
              Expanded(
                flex: 3,
                child: Padding(
                  padding: const EdgeInsets.all(10),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        title,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w700,
                          color: AppColors.textPrimary,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Row(
                        children: [
                          Icon(Icons.location_on_outlined, size: 12, color: AppColors.textHint),
                          const SizedBox(width: 3),
                          Expanded(
                            child: Text(
                              location,
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(
                                fontSize: 11,
                                color: AppColors.textSecondary,
                              ),
                            ),
                          ),
                        ],
                      ),
                      const Spacer(),
                      if (price != null)
                        Text(
                          _formatPrice(price),
                          style: const TextStyle(
                            fontSize: 12,
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
  }

  String _formatPrice(dynamic price) {
    if (price == null) return '';
    final p = double.tryParse(price.toString()) ?? 0;
    if (p >= 1000000) return '${(p / 1000000).toStringAsFixed(1)}M TZS';
    if (p >= 1000) return '${(p / 1000).toStringAsFixed(0)}K TZS';
    return '$p TZS';
  }
}

class _SearchPopup extends StatefulWidget {
  const _SearchPopup();

  @override
  State<_SearchPopup> createState() => _SearchPopupState();
}

class _SearchPopupState extends State<_SearchPopup> {
  final TextEditingController _searchController = TextEditingController();
  Timer? _debounce;
  List<Map<String, dynamic>> _results = [];
  bool _isLoading = false;
  bool _hasSearched = false;

  @override
  void dispose() {
    _searchController.dispose();
    _debounce?.cancel();
    super.dispose();
  }

  void _onSearchChanged(String query) {
    _debounce?.cancel();
    if (query.trim().isEmpty) {
      setState(() {
        _results = [];
        _hasSearched = false;
      });
      return;
    }
    _debounce = Timer(const Duration(milliseconds: 500), () {
      _performSearch(query.trim());
    });
  }

  Future<void> _performSearch(String query) async {
    setState(() => _isLoading = true);
    try {
      final data = await ApiService().get('properties?search=$query');
      final properties = data['data'] as List<dynamic>? ?? [];
      setState(() {
        _results = properties.cast<Map<String, dynamic>>();
        _hasSearched = true;
      });
    } catch (e) {
      setState(() {
        _results = [];
        _hasSearched = true;
      });
    } finally {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: Material(
        color: Colors.white,
        child: SizedBox(
          height: MediaQuery.of(context).size.height,
          child: Column(
            children: [
              // Search header
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                decoration: BoxDecoration(
                  color: Colors.white,
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withValues(alpha: 0.05),
                      blurRadius: 6,
                      offset: const Offset(0, 2),
                    ),
                  ],
                ),
                child: Row(
                  children: [
                    IconButton(
                      icon: const Icon(Icons.arrow_back, size: 22),
                      onPressed: () => Navigator.of(context).pop(),
                    ),
                    Expanded(
                      child: TextField(
                        controller: _searchController,
                        autofocus: true,
                        onChanged: _onSearchChanged,
                        decoration: InputDecoration(
                          hintText: 'Search properties, locations...',
                          hintStyle: TextStyle(
                            fontSize: 14,
                            color: AppColors.textHint,
                          ),
                          prefixIcon: Icon(Icons.search, size: 20, color: AppColors.textHint),
                          suffixIcon: _searchController.text.isNotEmpty
                              ? IconButton(
                                  icon: const Icon(Icons.clear, size: 18),
                                  onPressed: () {
                                    _searchController.clear();
                                    setState(() {
                                      _results = [];
                                      _hasSearched = false;
                                    });
                                  },
                                )
                              : null,
                          filled: true,
                          fillColor: AppColors.tealGreen50,
                          contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 0),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide.none,
                          ),
                          enabledBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide.none,
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(color: AppColors.tealGreen, width: 1),
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              // Results
              Expanded(
                child: _buildResults(),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildResults() {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    if (!_hasSearched) {
      return Center(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(Icons.search, size: 48, color: AppColors.textHint.withValues(alpha: 0.5)),
            const SizedBox(height: 12),
            Text(
              'Search for properties by name or location',
              style: TextStyle(
                fontSize: 14,
                color: AppColors.textHint,
              ),
            ),
          ],
        ),
      );
    }

    if (_results.isEmpty) {
      return Center(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(Icons.sentiment_dissatisfied, size: 48, color: AppColors.textHint.withValues(alpha: 0.5)),
            const SizedBox(height: 12),
            Text(
              'No properties found',
              style: TextStyle(
                fontSize: 14,
                color: AppColors.textHint,
              ),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _results.length,
      itemBuilder: (context, index) {
        final property = _results[index];
        final title = property['title'] ?? 'Untitled Property';
        final location = '${property['district'] ?? ''}, ${property['region'] ?? ''}';
        final price = property['price_min'] != null
            ? 'TSh ${property['price_min']}/mo'
            : 'Price on request';

        return Container(
          margin: const EdgeInsets.only(bottom: 12),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.04),
                blurRadius: 6,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          child: ListTile(
            contentPadding: const EdgeInsets.all(12),
            leading: Container(
              width: 56,
              height: 56,
              decoration: BoxDecoration(
                color: AppColors.tealGreen50,
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(Icons.home_work_outlined, color: AppColors.tealGreen, size: 24),
            ),
            title: Text(
              title.toString(),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w700,
                color: AppColors.textPrimary,
              ),
            ),
            subtitle: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 4),
                Row(
                  children: [
                    Icon(Icons.location_on_outlined, size: 12, color: AppColors.textHint),
                    const SizedBox(width: 3),
                    Expanded(
                      child: Text(
                        location,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                          fontSize: 12,
                          color: AppColors.textSecondary,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 4),
                Text(
                  price,
                  style: const TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.w700,
                    color: AppColors.tealGreen,
                  ),
                ),
              ],
            ),
            onTap: () {
              Navigator.of(context).pop();
            },
          ),
        );
      },
    );
  }
}

class _SearchPage extends StatelessWidget {
  const _SearchPage();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Search')),
      body: const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.search, size: 64, color: AppColors.textHint),
            SizedBox(height: 16),
            Text(
              'Search for properties',
              style: TextStyle(
                fontSize: 16,
                color: AppColors.textSecondary,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _FavoritesPage extends StatefulWidget {
  const _FavoritesPage();

  @override
  State<_FavoritesPage> createState() => _FavoritesPageState();
}

class _FavoritesPageState extends State<_FavoritesPage> {
  List<Map<String, dynamic>> _favorites = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchFavorites();
  }

  Future<void> _fetchFavorites() async {
    setState(() => _isLoading = true);
    try {
      final data = await ApiService().get('favorites');
      final favs = (data['data'] as List<dynamic>?) ?? [];
      setState(() {
        _favorites = favs.cast<Map<String, dynamic>>();
        _isLoading = false;
      });
    } catch (_) {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Saved Properties')),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _favorites.isEmpty
              ? RefreshIndicator(
                  onRefresh: _fetchFavorites,
                  child: ListView(
                    children: [
                      const SizedBox(height: 200),
                      Center(
                        child: Column(
                          children: [
                            Icon(Icons.favorite_border, size: 64, color: AppColors.textHint),
                            const SizedBox(height: 16),
                            Text(
                              'No saved properties yet',
                              style: TextStyle(
                                fontSize: 16,
                                color: AppColors.textSecondary,
                              ),
                            ),
                            const SizedBox(height: 8),
                            Text(
                              'Tap the heart icon on a property to save it here',
                              style: TextStyle(
                                fontSize: 13,
                                color: AppColors.textHint,
                              ),
                              textAlign: TextAlign.center,
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                )
              : RefreshIndicator(
                  onRefresh: _fetchFavorites,
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: _favorites.length,
                    itemBuilder: (context, index) {
                      final fav = _favorites[index];
                      final property = fav['property'] as Map<String, dynamic>?;
                      if (property == null) return const SizedBox.shrink();
                      final images = property['images'] as List<dynamic>?;
                      String? imgUrl;
                      if (images != null && images.isNotEmpty) {
                        final img = images[0] as Map<String, dynamic>;
                        imgUrl = img['url'] ?? img['image_url'];
                      }
                      final title = property['title'] ?? 'Untitled';
                      final region = property['region'] ?? '';
                      final district = property['district'] ?? '';
                      final price = property['price'];
                      final propertyType = property['property_type'] ?? '';
                      final isAvailable = property['is_available'] == true;

                      return GestureDetector(
                        onTap: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => PropertyDetailScreen(propertyId: property['id'] as int),
                            ),
                          ).then((_) => _fetchFavorites());
                        },
                        child: Container(
                          margin: const EdgeInsets.only(bottom: 14),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(14),
                            border: Border.all(color: AppColors.tealGreen100),
                          ),
                          child: ClipRRect(
                            borderRadius: BorderRadius.circular(14),
                            child: Row(
                              children: [
                                Container(
                                  width: 100,
                                  height: 100,
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
                                Expanded(
                                  child: Padding(
                                    padding: const EdgeInsets.all(12),
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          title,
                                          maxLines: 1,
                                          overflow: TextOverflow.ellipsis,
                                          style: TextStyle(
                                            fontSize: 15,
                                            fontWeight: FontWeight.w800,
                                            color: AppColors.textPrimary,
                                          ),
                                        ),
                                        const SizedBox(height: 4),
                                        Row(
                                          children: [
                                            Icon(Icons.location_on, size: 12, color: AppColors.textHint),
                                            const SizedBox(width: 2),
                                            Expanded(
                                              child: Text(
                                                '$region, $district',
                                                maxLines: 1,
                                                overflow: TextOverflow.ellipsis,
                                                style: TextStyle(
                                                  fontSize: 12,
                                                  color: AppColors.textSecondary,
                                                ),
                                              ),
                                            ),
                                          ],
                                        ),
                                        const SizedBox(height: 8),
                                        Row(
                                          children: [
                                            Container(
                                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                              decoration: BoxDecoration(
                                                color: AppColors.tealGreen50,
                                                borderRadius: BorderRadius.circular(6),
                                              ),
                                              child: Text(
                                                _capitalizeType(propertyType),
                                                style: TextStyle(
                                                  fontSize: 10,
                                                  fontWeight: FontWeight.w600,
                                                  color: AppColors.tealGreen,
                                                ),
                                              ),
                                            ),
                                            const SizedBox(width: 6),
                                            Container(
                                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                              decoration: BoxDecoration(
                                                color: isAvailable
                                                    ? const Color(0xFFe8f5e9)
                                                    : const Color(0xFFffebee),
                                                borderRadius: BorderRadius.circular(6),
                                              ),
                                              child: Text(
                                                isAvailable ? 'Available' : 'Occupied',
                                                style: TextStyle(
                                                  fontSize: 10,
                                                  fontWeight: FontWeight.w600,
                                                  color: isAvailable
                                                      ? const Color(0xFF2e7d32)
                                                      : const Color(0xFFc62828),
                                                ),
                                              ),
                                            ),
                                            const Spacer(),
                                            if (price != null)
                                              Text(
                                                _formatPrice(price),
                                                style: TextStyle(
                                                  fontSize: 13,
                                                  fontWeight: FontWeight.w700,
                                                  color: AppColors.tealGreen,
                                                ),
                                              ),
                                          ],
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                                Container(
                                  width: 40,
                                  height: 100,
                                  decoration: BoxDecoration(
                                    color: Colors.redAccent.withValues(alpha: 0.05),
                                    border: Border(
                                      left: BorderSide(color: AppColors.tealGreen100, width: 1),
                                    ),
                                  ),
                                  child: Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Icon(Icons.favorite, color: Colors.redAccent, size: 20),
                                      const SizedBox(height: 4),
                                      Text(
                                        'Saved',
                                        style: TextStyle(
                                          fontSize: 8,
                                          color: Colors.redAccent.withValues(alpha: 0.7),
                                          fontWeight: FontWeight.w600,
                                        ),
                                      ),
                                    ],
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
    );
  }

  String _capitalizeType(String s) {
    if (s.isEmpty) return s;
    return s[0].toUpperCase() + s.substring(1).replaceAll('_', ' ');
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
}

class _ProfilePage extends StatefulWidget {
  const _ProfilePage();

  @override
  State<_ProfilePage> createState() => _ProfilePageState();
}

class _ProfilePageState extends State<_ProfilePage> {
  Map<String, dynamic>? _user;
  bool _isLoading = true;
  int _unreadCount = 0;

  @override
  void initState() {
    super.initState();
    _fetchUser();
  }

  Future<void> _fetchUser() async {
    setState(() => _isLoading = true);
    try {
      final data = await ApiService().get('user');
      if (data['success'] == true && data['user'] != null) {
        final user = data['user'] as Map<String, dynamic>;
        await AuthService().updateUser(user);
        setState(() {
          _user = user;
          _isLoading = false;
        });
      } else {
        final savedUser = await AuthService().getUser();
        setState(() {
          _user = savedUser;
          _isLoading = false;
        });
      }
    } catch (_) {
      final savedUser = await AuthService().getUser();
      setState(() {
        _user = savedUser;
        _isLoading = false;
      });
    }
    _fetchUnreadCount();
  }

  Future<void> _fetchUnreadCount() async {
    try {
      final data = await ApiService().get('notifications/unread-count');
      if (mounted) {
        setState(() => _unreadCount = data['unread_count'] ?? 0);
      }
    } catch (_) {}
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F7FA),
      appBar: AppBar(
        title: Text('Profile', style: GoogleFonts.nunito(fontWeight: FontWeight.w700)),
        backgroundColor: AppColors.tealGreen,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: AppColors.tealGreen))
          : RefreshIndicator(
              onRefresh: _fetchUser,
              color: AppColors.tealGreen,
              child: ListView(
                children: [
                  _buildProfileHeader(),
                  const SizedBox(height: 16),
                  _buildInfoSection(),
                  const SizedBox(height: 16),
                  _buildMenuSection(),
                  const SizedBox(height: 24),
                  _buildLogoutButton(),
                  const SizedBox(height: 40),
                ],
              ),
            ),
    );
  }

  Widget _buildProfileHeader() {
    final name = _user?['name'] ?? 'User';
    final email = _user?['email'] ?? '';
    final avatarUrl = _user?['avatar_url'] as String?;
    final role = _user?['role'] ?? 'tenant';
    final kycStatus = _user?['kyc_status'] ?? 'pending';

    return Container(
      width: double.infinity,
      decoration: BoxDecoration(
        color: AppColors.tealGreen,
        borderRadius: const BorderRadius.only(
          bottomLeft: Radius.circular(28),
          bottomRight: Radius.circular(28),
        ),
      ),
      child: Padding(
        padding: const EdgeInsets.fromLTRB(24, 12, 24, 32),
        child: Column(
          children: [
            Container(
              width: 90,
              height: 90,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: Colors.white,
                border: Border.all(color: Colors.white, width: 3),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.15),
                    blurRadius: 12,
                    spreadRadius: 2,
                  ),
                ],
              ),
              child: ClipOval(
                child: avatarUrl != null && avatarUrl.isNotEmpty
                    ? Image.network(
                        avatarUrl,
                        fit: BoxFit.cover,
                        errorBuilder: (_, __, ___) => _buildAvatarPlaceholder(name),
                      )
                    : _buildAvatarPlaceholder(name),
              ),
            ),
            const SizedBox(height: 14),
            Text(
              name,
              style: GoogleFonts.nunito(
                fontSize: 22,
                fontWeight: FontWeight.w800,
                color: Colors.white,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              email,
              style: GoogleFonts.nunito(
                fontSize: 14,
                color: Colors.white.withValues(alpha: 0.8),
              ),
            ),
            const SizedBox(height: 12),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                _buildHeaderChip(_capitalizeRole(role), Icons.person_outline),
                const SizedBox(width: 8),
                _buildHeaderChip(
                  kycStatus == 'verified' ? 'KYC Verified' : 'KYC Pending',
                  kycStatus == 'verified' ? Icons.verified : Icons.hourglass_top,
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildAvatarPlaceholder(String name) {
    final initials = name.isNotEmpty
        ? name.split(' ').map((e) => e[0]).take(2).join().toUpperCase()
        : '?';
    return Container(
      color: AppColors.tealGreen50,
      child: Center(
        child: Text(
          initials,
          style: GoogleFonts.nunito(
            fontSize: 32,
            fontWeight: FontWeight.w800,
            color: AppColors.tealGreen,
          ),
        ),
      ),
    );
  }

  Widget _buildHeaderChip(String label, IconData icon) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: 0.15),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.white.withValues(alpha: 0.2), width: 1),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 12, color: Colors.white),
          const SizedBox(width: 4),
          Text(
            label,
            style: GoogleFonts.nunito(
              fontSize: 11,
              fontWeight: FontWeight.w600,
              color: Colors.white,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoSection() {
    final phone = _user?['phone'] ?? 'Not set';
    final region = _user?['region'] ?? 'Not set';
    final district = _user?['district'] ?? 'Not set';
    final email = _user?['email'] ?? 'Not set';

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.04),
              blurRadius: 10,
              offset: const Offset(0, 3),
            ),
          ],
        ),
        child: Column(
          children: [
            _buildInfoRow(Icons.email_outlined, 'Email', email),
            _buildDivider(),
            _buildInfoRow(Icons.phone_outlined, 'Phone', phone),
            _buildDivider(),
            _buildInfoRow(Icons.location_on_outlined, 'Region', region),
            _buildDivider(),
            _buildInfoRow(Icons.map_outlined, 'District', district),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      child: Row(
        children: [
          Container(
            width: 36,
            height: 36,
            decoration: BoxDecoration(
              color: AppColors.tealGreen50,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, size: 18, color: AppColors.tealGreen),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  label,
                  style: GoogleFonts.nunito(
                    fontSize: 11,
                    color: AppColors.textHint,
                    fontWeight: FontWeight.w500,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  value,
                  style: GoogleFonts.nunito(
                    fontSize: 14,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textPrimary,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDivider() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Divider(height: 1, color: AppColors.tealGreen100.withValues(alpha: 0.5)),
    );
  }

  Widget _buildMenuSection() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.04),
              blurRadius: 10,
              offset: const Offset(0, 3),
            ),
          ],
        ),
        child: Column(
          children: [
            _buildMenuTile(Icons.edit_outlined, 'Edit Profile', 'Update your personal information', () => _showEditProfileModal()),
            _buildMenuDivider(),
            _buildMenuTile(Icons.lock_outline, 'Change Password', 'Update your account password', () => _showChangePasswordModal()),
            _buildMenuDivider(),
            _buildNotificationMenuTile(),
            _buildMenuDivider(),
            _buildMenuTile(Icons.help_outline, 'Help & Support', 'Get help and contact us', () {}),
            _buildMenuDivider(),
            _buildMenuTile(Icons.info_outline, 'About ${AppConstants.appName}', 'App version and information', () {}),
          ],
        ),
      ),
    );
  }

  Widget _buildMenuTile(IconData icon, String title, String subtitle, VoidCallback onTap) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          child: Row(
            children: [
              Container(
                width: 36,
                height: 36,
                decoration: BoxDecoration(
                  color: AppColors.tealGreen50,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(icon, size: 18, color: AppColors.tealGreen),
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
                        fontWeight: FontWeight.w700,
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
              const Icon(Icons.chevron_right, color: AppColors.textHint, size: 20),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildMenuDivider() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Divider(height: 1, color: AppColors.tealGreen100.withValues(alpha: 0.5)),
    );
  }

  Widget _buildNotificationMenuTile() {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: () async {
          await Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => const NotificationsScreen()),
          );
          _fetchUnreadCount();
        },
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          child: Row(
            children: [
              Container(
                width: 36,
                height: 36,
                decoration: BoxDecoration(
                  color: AppColors.tealGreen50,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(Icons.notifications_outlined, size: 18, color: AppColors.tealGreen),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Notifications',
                      style: GoogleFonts.nunito(
                        fontSize: 14,
                        fontWeight: FontWeight.w700,
                        color: AppColors.textPrimary,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      _unreadCount > 0
                          ? 'You have $_unreadCount unread notification${_unreadCount > 1 ? 's' : ''}'
                          : 'View all your notifications',
                      style: GoogleFonts.nunito(
                        fontSize: 11,
                        color: _unreadCount > 0 ? AppColors.tealGreen : AppColors.textHint,
                        fontWeight: _unreadCount > 0 ? FontWeight.w600 : FontWeight.w400,
                      ),
                    ),
                  ],
                ),
              ),
              if (_unreadCount > 0)
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(
                    color: AppColors.error,
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Text(
                    _unreadCount > 99 ? '99+' : '$_unreadCount',
                    style: GoogleFonts.nunito(
                      fontSize: 10,
                      fontWeight: FontWeight.w800,
                      color: Colors.white,
                    ),
                  ),
                )
              else
                const Icon(Icons.chevron_right, color: AppColors.textHint, size: 20),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildLogoutButton() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: SizedBox(
        width: double.infinity,
        height: 52,
        child: OutlinedButton.icon(
          onPressed: () async {
            await AuthService().logout();
            if (mounted) {
              Navigator.of(context).pushNamedAndRemoveUntil('/login', (route) => false);
            }
          },
          icon: const Icon(Icons.logout, color: AppColors.error, size: 20),
          label: Text(
            'Logout',
            style: GoogleFonts.nunito(
              color: AppColors.error,
              fontWeight: FontWeight.w700,
              fontSize: 15,
            ),
          ),
          style: OutlinedButton.styleFrom(
            side: const BorderSide(color: AppColors.error, width: 1.5),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
            backgroundColor: Colors.white,
          ),
        ),
      ),
    );
  }

  void _showEditProfileModal() {
    final nameController = TextEditingController(text: _user?['name'] ?? '');
    final phoneController = TextEditingController(text: _user?['phone'] ?? '');
    final regionController = TextEditingController(text: _user?['region'] ?? '');
    final districtController = TextEditingController(text: _user?['district'] ?? '');
    final formKey = GlobalKey<FormState>();
    bool isSaving = false;

    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      isScrollControlled: true,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setModalState) => Container(
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
          ),
          child: Padding(
            padding: EdgeInsets.fromLTRB(
              20, 12, 20,
              20 + MediaQuery.of(ctx).viewInsets.bottom,
            ),
            child: Form(
              key: formKey,
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Center(
                    child: Container(
                      width: 40,
                      height: 4,
                      decoration: BoxDecoration(
                        color: Colors.grey[300],
                        borderRadius: BorderRadius.circular(2),
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                  Text(
                    'Edit Profile',
                    style: GoogleFonts.nunito(
                      fontSize: 20,
                      fontWeight: FontWeight.w800,
                      color: AppColors.textPrimary,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    'Update your personal information',
                    style: GoogleFonts.nunito(
                      fontSize: 13,
                      color: AppColors.textHint,
                    ),
                  ),
                  const SizedBox(height: 20),
                  _buildFormField(
                    controller: nameController,
                    label: 'Full Name',
                    icon: Icons.person_outline,
                    validator: (v) => v == null || v.isEmpty ? 'Name is required' : null,
                  ),
                  const SizedBox(height: 14),
                  _buildFormField(
                    controller: phoneController,
                    label: 'Phone Number',
                    icon: Icons.phone_outlined,
                    keyboardType: TextInputType.phone,
                  ),
                  const SizedBox(height: 14),
                  _buildFormField(
                    controller: regionController,
                    label: 'Region',
                    icon: Icons.location_on_outlined,
                  ),
                  const SizedBox(height: 14),
                  _buildFormField(
                    controller: districtController,
                    label: 'District',
                    icon: Icons.map_outlined,
                  ),
                  const SizedBox(height: 24),
                  Row(
                    children: [
                      Expanded(
                        child: TextButton(
                          onPressed: () => Navigator.pop(ctx),
                          child: Text(
                            'Cancel',
                            style: GoogleFonts.nunito(
                              fontWeight: FontWeight.w600,
                              color: AppColors.textSecondary,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: ElevatedButton(
                          onPressed: isSaving
                              ? null
                              : () async {
                                  if (!formKey.currentState!.validate()) return;
                                  setModalState(() => isSaving = true);
                                  try {
                                    final data = await ApiService().put(
                                      'user/profile',
                                      body: {
                                        'name': nameController.text.trim(),
                                        'phone': phoneController.text.trim(),
                                        'region': regionController.text.trim(),
                                        'district': districtController.text.trim(),
                                      },
                                    );
                                    if (data['success'] == true && data['user'] != null) {
                                      final updatedUser = data['user'] as Map<String, dynamic>;
                                      await AuthService().updateUser(updatedUser);
                                      if (mounted) {
                                        setState(() => _user = updatedUser);
                                      }
                                      if (ctx.mounted) {
                                        Navigator.pop(ctx);
                                        ScaffoldMessenger.of(context).showSnackBar(
                                          SnackBar(
                                            content: Text(data['message'] ?? 'Profile updated'),
                                            backgroundColor: AppColors.tealGreen,
                                            behavior: SnackBarBehavior.floating,
                                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                          ),
                                        );
                                      }
                                    }
                                  } catch (e) {
                                    if (ctx.mounted) {
                                      ScaffoldMessenger.of(context).showSnackBar(
                                        SnackBar(
                                          content: const Text('Failed to update profile'),
                                          backgroundColor: Colors.red,
                                          behavior: SnackBarBehavior.floating,
                                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                        ),
                                      );
                                    }
                                  }
                                  if (ctx.mounted) {
                                    setModalState(() => isSaving = false);
                                  }
                                },
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppColors.tealGreen,
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 14),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                          ),
                          child: isSaving
                              ? const SizedBox(
                                  width: 20,
                                  height: 20,
                                  child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                                )
                              : Text(
                                  'Save Changes',
                                  style: GoogleFonts.nunito(fontWeight: FontWeight.w700, fontSize: 14),
                                ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  void _showChangePasswordModal() {
    final currentController = TextEditingController();
    final newController = TextEditingController();
    final confirmController = TextEditingController();
    final formKey = GlobalKey<FormState>();
    bool isSaving = false;
    bool obscureCurrent = true;
    bool obscureNew = true;
    bool obscureConfirm = true;

    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      isScrollControlled: true,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setModalState) => Container(
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
          ),
          child: Padding(
            padding: EdgeInsets.fromLTRB(
              20, 12, 20,
              20 + MediaQuery.of(ctx).viewInsets.bottom,
            ),
            child: Form(
              key: formKey,
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Center(
                    child: Container(
                      width: 40,
                      height: 4,
                      decoration: BoxDecoration(
                        color: Colors.grey[300],
                        borderRadius: BorderRadius.circular(2),
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                  Text(
                    'Change Password',
                    style: GoogleFonts.nunito(
                      fontSize: 20,
                      fontWeight: FontWeight.w800,
                      color: AppColors.textPrimary,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    'Enter your current password and a new one',
                    style: GoogleFonts.nunito(
                      fontSize: 13,
                      color: AppColors.textHint,
                    ),
                  ),
                  const SizedBox(height: 20),
                  _buildPasswordField(
                    controller: currentController,
                    label: 'Current Password',
                    obscure: obscureCurrent,
                    onToggle: () => setModalState(() => obscureCurrent = !obscureCurrent),
                    validator: (v) => v == null || v.isEmpty ? 'Current password is required' : null,
                  ),
                  const SizedBox(height: 14),
                  _buildPasswordField(
                    controller: newController,
                    label: 'New Password',
                    obscure: obscureNew,
                    onToggle: () => setModalState(() => obscureNew = !obscureNew),
                    validator: (v) {
                      if (v == null || v.isEmpty) return 'New password is required';
                      if (v.length < 8) return 'Password must be at least 8 characters';
                      return null;
                    },
                  ),
                  const SizedBox(height: 14),
                  _buildPasswordField(
                    controller: confirmController,
                    label: 'Confirm New Password',
                    obscure: obscureConfirm,
                    onToggle: () => setModalState(() => obscureConfirm = !obscureConfirm),
                    validator: (v) {
                      if (v == null || v.isEmpty) return 'Please confirm your password';
                      if (v != newController.text) return 'Passwords do not match';
                      return null;
                    },
                  ),
                  const SizedBox(height: 24),
                  Row(
                    children: [
                      Expanded(
                        child: TextButton(
                          onPressed: () => Navigator.pop(ctx),
                          child: Text(
                            'Cancel',
                            style: GoogleFonts.nunito(
                              fontWeight: FontWeight.w600,
                              color: AppColors.textSecondary,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: ElevatedButton(
                          onPressed: isSaving
                              ? null
                              : () async {
                                  if (!formKey.currentState!.validate()) return;
                                  setModalState(() => isSaving = true);
                                  try {
                                    final data = await ApiService().put(
                                      'user/password',
                                      body: {
                                        'current_password': currentController.text,
                                        'password': newController.text,
                                        'password_confirmation': confirmController.text,
                                      },
                                    );
                                    if (ctx.mounted) {
                                      Navigator.pop(ctx);
                                      ScaffoldMessenger.of(context).showSnackBar(
                                        SnackBar(
                                          content: Text(data['message'] ?? 'Password changed successfully'),
                                          backgroundColor: AppColors.tealGreen,
                                          behavior: SnackBarBehavior.floating,
                                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                        ),
                                      );
                                    }
                                  } catch (e) {
                                    if (ctx.mounted) {
                                      ScaffoldMessenger.of(context).showSnackBar(
                                        SnackBar(
                                          content: const Text('Failed to change password. Check your current password.'),
                                          backgroundColor: Colors.red,
                                          behavior: SnackBarBehavior.floating,
                                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                        ),
                                      );
                                    }
                                  }
                                  if (ctx.mounted) {
                                    setModalState(() => isSaving = false);
                                  }
                                },
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppColors.tealGreen,
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 14),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                          ),
                          child: isSaving
                              ? const SizedBox(
                                  width: 20,
                                  height: 20,
                                  child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                                )
                              : Text(
                                  'Update Password',
                                  style: GoogleFonts.nunito(fontWeight: FontWeight.w700, fontSize: 14),
                                ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildFormField({
    required TextEditingController controller,
    required String label,
    required IconData icon,
    TextInputType? keyboardType,
    String? Function(String?)? validator,
  }) {
    return TextFormField(
      controller: controller,
      keyboardType: keyboardType,
      validator: validator,
      decoration: InputDecoration(
        labelText: label,
        prefixIcon: Icon(icon, size: 20, color: AppColors.tealGreen),
        labelStyle: GoogleFonts.nunito(fontSize: 13, color: AppColors.textHint),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: AppColors.tealGreen100),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: AppColors.tealGreen100),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppColors.tealGreen, width: 1.5),
        ),
        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
      ),
      style: GoogleFonts.nunito(fontSize: 14, fontWeight: FontWeight.w600, color: AppColors.textPrimary),
    );
  }

  Widget _buildPasswordField({
    required TextEditingController controller,
    required String label,
    required bool obscure,
    required VoidCallback onToggle,
    String? Function(String?)? validator,
  }) {
    return TextFormField(
      controller: controller,
      obscureText: obscure,
      validator: validator,
      decoration: InputDecoration(
        labelText: label,
        prefixIcon: const Icon(Icons.lock_outline, size: 20, color: AppColors.tealGreen),
        suffixIcon: IconButton(
          icon: Icon(
            obscure ? Icons.visibility_off_outlined : Icons.visibility_outlined,
            size: 20,
            color: AppColors.textHint,
          ),
          onPressed: onToggle,
        ),
        labelStyle: GoogleFonts.nunito(fontSize: 13, color: AppColors.textHint),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: AppColors.tealGreen100),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: AppColors.tealGreen100),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppColors.tealGreen, width: 1.5),
        ),
        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
      ),
      style: GoogleFonts.nunito(fontSize: 14, fontWeight: FontWeight.w600, color: AppColors.textPrimary),
    );
  }

  String _capitalizeRole(String role) {
    if (role.isEmpty) return role;
    return role[0].toUpperCase() + role.substring(1);
  }
}
