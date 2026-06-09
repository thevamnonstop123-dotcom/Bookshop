<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Author;
use App\Models\Book;
use App\Models\Banner;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class BookshopDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Authors
        $authorNames = ['Robert C. Martin','James Clear','Martin Kleppmann','Yuval Noah Harari','Stephen Hawking','Dale Carnegie','George Orwell','Cal Newport','Andrew S. Tanenbaum','Charles Petzold'];
        foreach ($authorNames as $name) {
            Author::firstOrCreate(['name' => $name], ['bio' => 'Author', 'image' => 'default.png', 'status' => 'active']);
        }

        // Categories
        $catNames = ['Programming','Business','Science','History','Self Development','Literature'];
        foreach ($catNames as $name) {
            Category::firstOrCreate(['name' => $name], ['description' => 'Books about ' . $name, 'status' => 'active']);
        }

        // Books with Open Library cover IDs
        $books = [
            ['cat'=>'Programming','auth'=>['Robert C. Martin'],'title'=>'Clean Code','isbn'=>'978-0132350884','price'=>35000,'stock'=>25,'lang'=>'English','date'=>'2008-08-01','cover'=>'https://covers.openlibrary.org/b/isbn/9780132350884-L.jpg'],
            ['cat'=>'Programming','auth'=>['Robert C. Martin'],'title'=>'The Clean Coder','isbn'=>'978-0137081073','price'=>32000,'stock'=>20,'lang'=>'English','date'=>'2011-05-13','cover'=>'https://covers.openlibrary.org/b/isbn/9780137081073-L.jpg'],
            ['cat'=>'Programming','auth'=>['Martin Kleppmann'],'title'=>'Designing Data-Intensive Applications','isbn'=>'978-1449373320','price'=>45000,'stock'=>15,'lang'=>'English','date'=>'2017-03-16','cover'=>'https://covers.openlibrary.org/b/isbn/9781449373320-L.jpg'],
            ['cat'=>'Programming','auth'=>['Andrew S. Tanenbaum'],'title'=>'Modern Operating Systems','isbn'=>'978-0133591620','price'=>52000,'stock'=>10,'lang'=>'English','date'=>'2014-03-07','cover'=>'https://covers.openlibrary.org/b/isbn/9780133591620-L.jpg'],
            ['cat'=>'Programming','auth'=>['Charles Petzold'],'title'=>'Code','isbn'=>'978-0735611313','price'=>28000,'stock'=>18,'lang'=>'English','date'=>'2000-10-11','cover'=>'https://covers.openlibrary.org/b/isbn/9780735611313-L.jpg'],
            ['cat'=>'Business','auth'=>['James Clear'],'title'=>'Atomic Habits','isbn'=>'978-0735211292','price'=>25000,'stock'=>40,'lang'=>'English','date'=>'2018-10-16','cover'=>'https://covers.openlibrary.org/b/isbn/9780735211292-L.jpg'],
            ['cat'=>'Business','auth'=>['Dale Carnegie'],'title'=>'How to Win Friends','isbn'=>'978-0671027032','price'=>18000,'stock'=>35,'lang'=>'English','date'=>'1936-10-01','cover'=>'https://covers.openlibrary.org/b/isbn/9780671027032-L.jpg'],
            ['cat'=>'Business','auth'=>['Cal Newport'],'title'=>'Deep Work','isbn'=>'978-1455586691','price'=>22000,'stock'=>22,'lang'=>'English','date'=>'2016-01-05','cover'=>'https://covers.openlibrary.org/b/isbn/9781455586691-L.jpg'],
            ['cat'=>'Science','auth'=>['Stephen Hawking'],'title'=>'A Brief History of Time','isbn'=>'978-0553380163','price'=>20000,'stock'=>30,'lang'=>'English','date'=>'1988-03-01','cover'=>'https://covers.openlibrary.org/b/isbn/9780553380163-L.jpg'],
            ['cat'=>'Science','auth'=>['Stephen Hawking'],'title'=>'The Universe in a Nutshell','isbn'=>'978-0553802023','price'=>24000,'stock'=>15,'lang'=>'English','date'=>'2001-11-06','cover'=>'https://covers.openlibrary.org/b/isbn/9780553802023-L.jpg'],
            ['cat'=>'History','auth'=>['Yuval Noah Harari'],'title'=>'Sapiens','isbn'=>'978-0062316097','price'=>28000,'stock'=>50,'lang'=>'English','date'=>'2015-02-10','cover'=>'https://covers.openlibrary.org/b/isbn/9780062316097-L.jpg'],
            ['cat'=>'History','auth'=>['Yuval Noah Harari'],'title'=>'Homo Deus','isbn'=>'978-0062464316','price'=>30000,'stock'=>28,'lang'=>'English','date'=>'2017-02-21','cover'=>'https://covers.openlibrary.org/b/isbn/9780062464316-L.jpg'],
            ['cat'=>'Self Development','auth'=>['Cal Newport'],'title'=>'Digital Minimalism','isbn'=>'978-0525536512','price'=>20000,'stock'=>20,'lang'=>'English','date'=>'2019-02-05','cover'=>'https://covers.openlibrary.org/b/isbn/9780525536512-L.jpg'],
            ['cat'=>'Literature','auth'=>['George Orwell'],'title'=>'1984','isbn'=>'978-0451524935','price'=>15000,'stock'=>60,'lang'=>'English','date'=>'1949-06-08','cover'=>'https://covers.openlibrary.org/b/isbn/9780451524935-L.jpg'],
            ['cat'=>'Literature','auth'=>['George Orwell'],'title'=>'Animal Farm','isbn'=>'978-0451526342','price'=>12000,'stock'=>45,'lang'=>'English','date'=>'1945-08-17','cover'=>'https://covers.openlibrary.org/b/isbn/9780451526342-L.jpg'],
        ];

        foreach ($books as $bookData) {
            $cat = Category::where('name', $bookData['cat'])->first();

            // Download cover image
            $imagePath = null;
            try {
                $contents = @file_get_contents($bookData['cover']);
                if ($contents) {
                    $filename = 'books/' . Str::slug($bookData['title']) . '.jpg';
                    Storage::disk('public')->put($filename, $contents);
                    $imagePath = $filename;
                    echo "Downloaded: " . $bookData['title'] . "\n";
                }
            } catch (\Exception $e) {
                echo "Failed: " . $bookData['title'] . "\n";
            }

            $book = Book::firstOrCreate(['isbn' => $bookData['isbn']], [
                'category_id' => $cat->id,
                'title' => $bookData['title'],
                'slug' => Str::slug($bookData['title']),
                'price' => $bookData['price'],
                'stock_quantity' => $bookData['stock'],
                'language' => $bookData['lang'],
                'published_date' => $bookData['date'],
                'description' => 'A great book about ' . $bookData['cat'],
                'image' => $imagePath ?? 'default.png',
                'status' => 'active',
            ]);

            $authIds = Author::whereIn('name', $bookData['auth'])->pluck('id')->toArray();
            $book->authors()->syncWithoutDetaching($authIds);
        }

        // Discounts
        Book::where('title', 'Atomic Habits')->update(['sale_price' => 20000, 'sale_starts_at' => now()->subDay(), 'sale_ends_at' => now()->addDays(30)]);
        Book::where('title', '1984')->update(['sale_price' => 10000, 'sale_starts_at' => now()->subDay(), 'sale_ends_at' => now()->addDays(14)]);
        Book::where('title', 'Clean Code')->update(['sale_price' => 28000, 'sale_starts_at' => now()->subDay(), 'sale_ends_at' => now()->addDays(7)]);

        // Banners
        if (Banner::count() == 0) {
            Banner::create(['title' => 'New Arrivals', 'description' => 'Level up your coding skills!', 'display_order' => 0, 'start_date' => now()->subDay(), 'end_date' => now()->addDays(30), 'status' => 'active', 'image' => 'default.png']);
            Banner::create(['title' => 'Summer Sale', 'description' => 'Get 20% off on bestsellers!', 'display_order' => 1, 'start_date' => now()->subDay(), 'end_date' => now()->addDays(15), 'status' => 'active', 'image' => 'default.png']);
        }

        echo "\n✅ Done! Books: " . Book::count() . " | Authors: " . Author::count() . " | Categories: " . Category::count() . " | Banners: " . Banner::count() . "\n";
    }
}
