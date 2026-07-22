<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\District;
use Illuminate\Database\Seeder;

class RegionDistrictSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Dar es Salaam' => ['Ilala', 'Kinondoni', 'Temeke', 'Ubungo', 'Kigamboni'],
            'Dodoma' => ['Dodoma Urban', 'Bahi', 'Chamwino', 'Chemba', 'Kondoa', 'Kongwa', 'Mpwapwa'],
            'Arusha' => ['Arusha Urban', 'Arusha Rural', 'Karatu', 'Longido', 'Monduli', 'Ngorongoro', 'Meru'],
            'Kilimanjaro' => ['Moshi Urban', 'Moshi Rural', 'Hai', 'Mwanga', 'Same', 'Rombo', 'Siha'],
            'Tanga' => ['Tanga Urban', 'Tanga Rural', 'Handeni', 'Kilindi', 'Korogwe', 'Lushoto', 'Muheza', 'Pangani', 'Mkinga'],
            'Morogoro' => ['Morogoro Urban', 'Morogoro Rural', 'Gairo', 'Ifakara', 'Kilombero', 'Kilosa', 'Mahenge', 'Malinyi', 'Mvomero', 'Ulanga'],
            'Pwani' => ['Bagamoyo', 'Kibaha', 'Kisarawe', 'Mafia', 'Mkuranga', 'Rufiji', 'Chalinze', 'Mlandizi'],
            'Mtwara' => ['Mtwara Urban', 'Mtwara Rural', 'Masasi', 'Newala', 'Tandahimba', 'Nanyumbu'],
            'Lindi' => ['Lindi Urban', 'Lindi Rural', 'Kilwa', 'Liwale', 'Nachingwea', 'Ruangwa', 'Mtama'],
            'Ruvuma' => ['Songea Urban', 'Songea Rural', 'Mbinga', 'Namtumbo', 'Tunduru', 'Namtumbo'],
            'Iringa' => ['Iringa Urban', 'Iringa Rural', 'Kilolo', 'Mafinga', 'Mufindi'],
            'Mbeya' => ['Mbeya Urban', 'Mbeya Rural', 'Chunya', 'Ileje', 'Kyela', 'Mbarali', 'Rungwe', 'Busokelo'],
            'Njombe' => ['Njombe Urban', 'Njombe Rural', 'Ludewa', 'Makambako', 'Makete', 'Wanging\'ombe'],
            'Songwe' => ['Mbeya Urban', 'Mbozi', 'Ileje', 'Momba', 'Songwe'],
            'Katavi' => ['Mpanda', 'Nsimbo', 'Mlele'],
            'Rukwa' => ['Sumbawanga Urban', 'Sumbawanga Rural', 'Kalambo', 'Nkasi', 'Mpanda'],
            'Kigoma' => ['Kigoma Urban', 'Kigoma Rural', 'Buhigwe', 'Kakonko', 'Kasulu', 'Kibondo', 'Uvinza'],
            'Tabora' => ['Tabora Urban', 'Tabora Rural', 'Igunga', 'Kaliua', 'Nzega', 'Sikonge', 'Uyui', 'Urambo'],
            'Shinyanga' => ['Shinyanga Urban', 'Shinyanga Rural', 'Kahama', 'Kishapu', 'Msalala'],
            'Simiyu' => ['Bariadi', 'Busega', 'Itilima', 'Meatu', 'Maswa'],
            'Kagera' => ['Bukoba Urban', 'Bukoba Rural', 'Biharamulo', 'Chato', 'Karagwe', 'Kyerwa', 'Muleba', 'Ngara'],
            'Geita' => ['Geita', 'Bukombe', 'Chato', 'Mbogwe', 'Nyang\'hwale'],
            'Mwanza' => ['Mwanza Urban', 'Ilemela', 'Kwimba', 'Magu', 'Misungwi', 'Nyamagana', 'Sengerema', 'Ukerewe'],
            'Mara' => ['Musoma Urban', 'Musoma Rural', 'Bunda', 'Butiama', 'Rorya', 'Serengeti', 'Tarime'],
            'Manyara' => ['Babati', 'Hanang', 'Kiteto', 'Mbulu', 'Simanjiro'],
            'Singida' => ['Singida Urban', 'Singida Rural', 'Iramba', 'Manyoni', 'Mkalama', 'Ikungi'],
        ];

        $order = 1;
        foreach ($data as $regionName => $districts) {
            $region = Region::create([
                'name' => $regionName,
                'code' => strtoupper(substr(str_replace(' ', '', $regionName), 0, 3)),
                'sort_order' => $order++,
                'is_active' => true,
            ]);

            $distOrder = 1;
            foreach ($districts as $districtName) {
                District::create([
                    'region_id' => $region->id,
                    'name' => $districtName,
                    'sort_order' => $distOrder++,
                    'is_active' => true,
                ]);
            }
        }
    }
}
