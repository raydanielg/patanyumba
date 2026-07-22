@extends('layouts.admin')

@section('title', 'My Profile - Patanyumba')
@section('page_title', 'My Profile')

@section('content')
{{-- AJAX Toast --}}
<div id="ajaxToast" class="fixed top-6 right-6 z-50 hidden bg-emerald-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm font-medium flex items-center gap-2 transition-all duration-300">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span id="ajaxToastMsg"></span>
</div>

<div class="max-w-4xl mx-auto space-y-6">

    {{-- Profile Header Card --}}
    <div class="bg-gradient-to-br from-emerald-700 to-emerald-900 rounded-2xl p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -mr-24 -mt-24"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-center gap-6">
            {{-- Avatar --}}
            <div class="relative group">
                <div class="w-28 h-28 rounded-full overflow-hidden border-4 border-white/20 shadow-xl bg-white/10 flex items-center justify-center">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover" id="profileAvatarImg">
                    @else
                        <span class="text-4xl font-bold text-white" id="profileAvatarInitials">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <button onclick="document.getElementById('avatarInput').click()" class="absolute bottom-1 right-1 w-9 h-9 rounded-full bg-white text-emerald-700 flex items-center justify-center shadow-lg hover:scale-110 transition-transform" title="Upload Avatar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </button>
                <input type="file" id="avatarInput" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" onchange="uploadAvatar(this)">
            </div>

            {{-- Name & Info --}}
            <div class="text-center sm:text-left flex-1">
                <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                <p class="text-emerald-200/80 text-sm mt-1">{{ $user->email }}</p>
                <div class="flex flex-wrap items-center gap-2 mt-3 justify-center sm:justify-start">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-white/15 text-white border border-white/20">
                        {{ $user->role }}
                    </span>
                    @if($user->isVerified())
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-gold-500/20 text-gold-100 border border-gold-400/30">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.062-.18-2.087-.514-3.056z"/></svg>
                        KYC Verified
                    </span>
                    @endif
                    @if($user->phone)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-medium bg-white/10 text-emerald-100 border border-white/15">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $user->phone }}
                    </span>
                    @endif
                </div>
            </div>

            {{-- Remove Avatar Button --}}
            @if($user->avatar_url)
            <button onclick="removeAvatar()" class="self-start text-xs text-emerald-200/60 hover:text-white transition-colors flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Remove
            </button>
            @endif
        </div>
    </div>

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Personal Information --}}
        <div class="lg:col-span-2 bg-white rounded-xl border p-6">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Personal Information</h3>
            </div>

            <form id="profileForm" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ $user->name }}" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ $user->email }}" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Phone Number</label>
                        <div class="relative">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <input type="text" name="phone" value="{{ $user->phone ?? '' }}" placeholder="+255 7XX XXX XXX" class="w-full pl-9 pr-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Business Name</label>
                        <input type="text" name="business_name" value="{{ $user->business_name ?? '' }}" placeholder="Optional" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Mkoa (Region)</label>
                        <select name="region" id="profileRegion" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                            <option value="">-- Chagua Mkoa --</option>
                            @php
                            $tanzaniaRegions = [
                                'Dar es Salaam' => ['Ilala', 'Kinondoni', 'Temeke', 'Kigamboni', 'Ubungo'],
                                'Dodoma' => ['Dodoma Urban', 'Bahi', 'Chamwino', 'Chemba', 'Kondoa', 'Kongwa', 'Mpwapwa'],
                                'Arusha' => ['Arusha Urban', 'Arumeru', 'Karatu', 'Longido', 'Monduli', 'Ngorongoro'],
                                'Mwanza' => ['Ilemela', 'Nyamagana', 'Geita', 'Kwimba', 'Magu', 'Misungwi', 'Sengerema', 'Ukerewe'],
                                'Tanga' => ['Tanga Urban', 'Handeni', 'Kilindi', 'Korogwe', 'Lushoto', 'Mkinga', 'Muheza', 'Pangani'],
                                'Kilimanjaro' => ['Moshi Urban', 'Hai', 'Mwanga', 'Rombo', 'Same', 'Siha'],
                                'Morogoro' => ['Morogoro Urban', 'Gairo', 'Ifakara', 'Kilombero', 'Kilosa', 'Mahenge', 'Malinyi', 'Mvomero', 'Ulanga'],
                                'Mbeya' => ['Mbeya Urban', 'Chunya', 'Ileje', 'Kyela', 'Mbarali', 'Mbozi', 'Rungwe'],
                                'Zanzibar' => ['Mjini Magharibi', 'Kaskazini A', 'Kaskazini B', 'Kusini', 'Kati', 'Kusini Unguja'],
                                'Pwani' => ['Bagamoyo', 'Kibaha', 'Kisarawe', 'Mafia', 'Mkuranga', 'Rufiji'],
                                'Kagera' => ['Bukoba Urban', 'Bukoba Rural', 'Biharamulo', 'Karagwe', 'Kyerwa', 'Muleba', 'Ngara'],
                                'Tabora' => ['Tabora Urban', 'Igunga', 'Kaliua', 'Nzega', 'Sikonge', 'Urambo'],
                                'Rukwa' => ['Sumbawanga Urban', 'Kalambo', 'Lyamba Lya Mfipa', 'Nkasi', 'Sumbawanga Rural'],
                                'Kigoma' => ['Kigoma Urban', 'Buhigwe', 'Kakonko', 'Kasulu', 'Kibondo', 'Uvinza'],
                                'Shinyanga' => ['Shinyanga Urban', 'Kahama', 'Kishapu', 'Shinyanga Rural'],
                                'Singida' => ['Singida Urban', 'Iramba', 'Manyoni', 'Mkalama', 'Singida Rural'],
                                'Iringa' => ['Iringa Urban', 'Iringa Rural', 'Kilolo', 'Ludewa', 'Makete', 'Mufindi', 'Njombe'],
                                'Ruvuma' => ['Songea Urban', 'Mbinga', 'Namtumbo', 'Nyasa', 'Songea Rural', 'Tunduru'],
                                'Mara' => ['Musoma Urban', 'Bunda', 'Butiama', 'Musoma Rural', 'Rorya', 'Serengeti', 'Tarime'],
                                'Lindi' => ['Lindi Urban', 'Kilwa', 'Lindi Rural', 'Liwale', 'Nachingwea', 'Ruangwa'],
                                'Mtwara' => ['Mtwara Urban', 'Masasi', 'Mtwara Rural', 'Nanyamba', 'Newala', 'Tandahimba'],
                                'Geita' => ['Geita Urban', 'Bukombe', 'Chato', 'Geita Rural', 'Mbogwe', 'Nyang\'hwale'],
                                'Njombe' => ['Njombe Urban', 'Ludewa', 'Makete', 'Njombe Rural', 'Wanging\'ombe'],
                                'Simiyu' => ['Bariadi', 'Busega', 'Itilima', 'Meatu', 'Simiyu'],
                                'Katavi' => ['Mpanda Urban', 'Mlele', 'Mpanda Rural', 'Nsimbo', 'Palahali'],
                                'Songwe' => ['Mbeya Urban', 'Ileje', 'Mbozi', 'Momba', 'Songwe'],
                                'Manyara' => ['Babati Urban', 'Babati Rural', 'Hanang', 'Kiteto', 'Mbulu', 'Simanjiro'],
                            ];
                            @endphp
                            @foreach($tanzaniaRegions as $regionName => $districts)
                            <option value="{{ $regionName }}" @if($user->region === $regionName) selected @endif>{{ $regionName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Wilaya (District)</label>
                        <select name="district" id="profileDistrict" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" @if(!$user->region) disabled @endif>
                            <option value="">-- Chagua Wilaya --</option>
                            @if($user->region && isset($tanzaniaRegions[$user->region]))
                                @foreach($tanzaniaRegions[$user->region] as $d)
                                <option value="{{ $d }}" @if($user->district === $d) selected @endif>{{ $d }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Address</label>
                        <textarea name="address" rows="2" placeholder="Your physical address" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">{{ $user->address ?? '' }}</textarea>
                    </div>
                </div>

                <div id="profileErrors" class="hidden bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-600"></div>

                <div class="flex items-center gap-3 pt-2 border-t">
                    <button type="submit" id="profileSaveBtn" class="px-6 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-lg hover:from-emerald-700 hover:to-emerald-900 flex items-center gap-2 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Changes
                    </button>
                    <p class="text-[10px] text-gray-400">Update your personal information</p>
                </div>
            </form>
        </div>

        {{-- Account Summary & Password --}}
        <div class="space-y-6">
            {{-- Account Summary --}}
            <div class="bg-white rounded-xl border p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Account Summary</h3>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-xs">Role</span>
                        <span class="font-semibold capitalize text-gray-900 text-xs px-2 py-0.5 rounded-full
                            @if($user->role==='admin') bg-emerald-50 text-emerald-700
                            @elseif($user->role==='landlord') bg-amber-50 text-amber-700
                            @elseif($user->role==='agent') bg-sky-50 text-sky-700
                            @else bg-gray-50 text-gray-700 @endif">{{ $user->role }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-xs">KYC Status</span>
                        <span class="font-medium capitalize text-xs
                            @if($user->kyc_status==='approved') text-emerald-600
                            @elseif($user->kyc_status==='pending') text-amber-600
                            @elseif($user->kyc_status==='rejected') text-red-500
                            @else text-gray-500 @endif">{{ $user->kyc_status }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-xs">Verification</span>
                        <span class="font-medium capitalize text-xs text-gray-700">{{ $user->verification_level }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-xs">Status</span>
                        <span class="font-medium text-xs {{ $user->is_active ? 'text-emerald-600' : 'text-red-500' }}">{{ $user->is_active ? 'Active' : 'Suspended' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-xs">Joined</span>
                        <span class="text-xs text-gray-700">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="bg-white rounded-xl border p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Change Password</h3>
                </div>
                <form id="passwordForm" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Current Password</label>
                        <input type="password" name="current_password" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">New Password</label>
                        <input type="password" name="password" required minlength="6" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Confirm Password</label>
                        <input type="password" name="password_confirmation" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    </div>
                    <div id="passwordErrors" class="hidden bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-600"></div>
                    <button type="submit" id="passwordSaveBtn" class="w-full px-4 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-amber-500 to-amber-600 rounded-lg hover:from-amber-600 hover:to-amber-700 flex items-center justify-center gap-2 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012-2m-4 0a2 2 0 012 2m0 0v6m0 0a2 2 0 012 2m-2-2a2 2 0 01-2 2m0 0v6m0 0a2 2 0 01-2 2m2-2a2 2 0 012 2"/></svg>
                        Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';

function showToast(msg, type = 'success') {
    const toast = document.getElementById('ajaxToast');
    document.getElementById('ajaxToastMsg').textContent = msg;
    toast.classList.remove('hidden'); toast.classList.add('flex');
    toast.style.transform = 'translateY(0)'; toast.style.opacity = '1';
    toast.className = toast.className.replace(/bg-(emerald|red|amber)-\d+/g, '');
    toast.classList.add(type === 'error' ? 'bg-red-500' : type === 'warning' ? 'bg-amber-500' : 'bg-emerald-600');
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(-10px)'; setTimeout(() => toast.classList.add('hidden'), 300); }, 3000);
}

// Avatar upload
async function uploadAvatar(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    if (file.size > 2048 * 1024) { showToast('File too large (max 2MB)', 'error'); return; }

    const formData = new FormData();
    formData.append('avatar', file);

    try {
        const res = await fetch('{{ route("profile.avatar") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: formData
        });
        const data = await res.json();
        if (res.ok && data.success) {
            const img = document.getElementById('profileAvatarImg');
            const initials = document.getElementById('profileAvatarInitials');
            if (img) { img.src = data.avatar_url; }
            else {
                const container = img ? img.parentElement : document.querySelector('#profileAvatarImg')?.parentElement || input.closest('.group').querySelector('.w-28');
                if (container) {
                    container.innerHTML = '<img src="' + data.avatar_url + '" alt="{{ $user->name }}" class="w-full h-full object-cover" id="profileAvatarImg">';
                }
            }
            showToast(data.message);
        } else {
            showToast(data.message || 'Upload failed', 'error');
        }
    } catch { showToast('Network error', 'error'); }
    input.value = '';
}

// Remove avatar
async function removeAvatar() {
    if (!confirm('Remove your avatar?')) return;
    try {
        const res = await fetch('{{ route("profile.avatar.remove") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
            location.reload();
        } else { showToast(data.message || 'Failed', 'error'); }
    } catch { showToast('Network error', 'error'); }
}

// Profile update
document.getElementById('profileForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('profileSaveBtn');
    const orig = btn.innerHTML;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path stroke-width="3" d="M4 12a8 8 0 018-8"/></svg> Saving...';
    btn.disabled = true;
    try {
        const res = await fetch('{{ route("profile.update") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: new FormData(this)
        });
        const data = await res.json();
        if (res.ok && data.success) {
            showToast(data.message);
            document.getElementById('profileErrors').classList.add('hidden');
        } else {
            const errs = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Error');
            const el = document.getElementById('profileErrors');
            el.innerHTML = errs; el.classList.remove('hidden');
            if (data.message && !data.errors) showToast(data.message, 'error');
        }
    } catch { showToast('Network error', 'error'); }
    btn.innerHTML = orig; btn.disabled = false;
});

// Password update
document.getElementById('passwordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('passwordSaveBtn');
    const orig = btn.innerHTML;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path stroke-width="3" d="M4 12a8 8 0 018-8"/></svg> Updating...';
    btn.disabled = true;
    try {
        const res = await fetch('{{ route("profile.password") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: new FormData(this)
        });
        const data = await res.json();
        if (res.ok && data.success) {
            showToast(data.message);
            this.reset();
            document.getElementById('passwordErrors').classList.add('hidden');
        } else {
            const errs = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Error');
            const el = document.getElementById('passwordErrors');
            el.innerHTML = errs; el.classList.remove('hidden');
        }
    } catch { showToast('Network error', 'error'); }
    btn.innerHTML = orig; btn.disabled = false;
});
</script>
@endsection
