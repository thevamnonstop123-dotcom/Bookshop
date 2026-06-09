<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MoreBooksSeeder extends Seeder
{
    public function run(): void
    {
        // Add Finance category
        Category::firstOrCreate(['name' => 'Finance'], ['description' => 'Financial literacy and investment books', 'status' => 'active']);

        // Add more authors
        $authors = [
            ['name' => 'Robert Kiyosaki', 'bio' => 'Author of Rich Dad Poor Dad'],
            ['name' => 'Napoleon Hill', 'bio' => 'Author of Think and Grow Rich'],
            ['name' => 'Simon Sinek', 'bio' => 'Author of Start With Why'],
            ['name' => 'Daniel Kahneman', 'bio' => 'Nobel Prize winner'],
            ['name' => 'Morgan Housel', 'bio' => 'Author of The Psychology of Money'],
            ['name' => 'Timothy Ferriss', 'bio' => 'Author of The 4-Hour Work Week'],
            ['name' => 'Ray Dalio', 'bio' => 'Billionaire investor, author of Principles'],
        ];
        foreach ($authors as $a) {
            Author::firstOrCreate(['name' => $a['name']], ['bio' => $a['bio'], 'image' => 'default.png', 'status' => 'active']);
        }

        // Books
        $books = [
            ['cat'=>'Business','auth'=>['Simon Sinek'],'title'=>'Start With Why','isbn'=>'978-1591846444','price'=>22000,'stock'=>25,'lang'=>'English','date'=>'2011-12-27','cover'=>'https://covers.openlibrary.org/b/isbn/9781591846444-L.jpg'],
            ['cat'=>'Business','auth'=>['Timothy Ferriss'],'title'=>'The 4-Hour Work Week','isbn'=>'978-0307465351','price'=>24000,'stock'=>20,'lang'=>'English','date'=>'2009-12-15','cover'=>'https://covers.openlibrary.org/b/isbn/9780307465351-L.jpg'],
            ['cat'=>'Self Development','auth'=>['Napoleon Hill'],'title'=>'Think and Grow Rich','isbn'=>'978-1937879501','price'=>18000,'stock'=>35,'lang'=>'English','date'=>'1937-01-01','cover'=>'https://covers.openlibrary.org/b/isbn/9781937879501-L.jpg'],
            ['cat'=>'Self Development','auth'=>['Daniel Kahneman'],'title'=>'Thinking Fast and Slow','isbn'=>'978-0374533557','price'=>26000,'stock'=>22,'lang'=>'English','date'=>'2013-04-02','cover'=>'https://covers.openlibrary.org/b/isbn/9780374533557-L.jpg'],
            ['cat'=>'Finance','auth'=>['Robert Kiyosaki'],'title'=>'Rich Dad Poor Dad','isbn'=>'978-1612680194','price'=>20000,'stock'=>40,'lang'=>'English','date'=>'2017-04-11','cover'=>'https://covers.openlibrary.org/b/isbn/9781612680194-L.jpg'],
            ['cat'=>'Finance','auth'=>['Morgan Housel'],'title'=>'The Psychology of Money','isbn'=>'978-0857197689','price'=>22000,'stock'=>30,'lang'=>'English','date'=>'2020-09-08','cover'=>'https://covers.openlibrary.org/b/isbn/9780857197689-L.jpg'],
            ['cat'=>'Finance','auth'=>['Ray Dalio'],'title'=>'Principles','isbn'=>'978-1501124020','price'=>35000,'stock'=>15,'lang'=>'English','date'=>'2017-09-19','cover'=>'https://covers.openlibrary.org/b/isbn/9781501124020-L.jpg'],
        ];

        foreach ($books as $b) {
            $cat = Category::where('name', $b['cat'])->first();

            $imagePath = null;
            try {
                $contents = @file_get_contents($b['cover']);
                if ($contents) {
                    $filename = 'books/' . Str::slug($b['title']) . '.jpg';
                    Storage::disk('public')->put($filename, $contents);
                    $imagePath = $filename;
                    echo "Downloaded: " . $b['title'] . "\n";
                }
            } catch (\Exception $e) {
                echo "Failed: " . $b['title'] . "\n";
            }

            $book = Book::firstOrCreate(['isbn' => $b['isbn']], [
                'category_id' => $cat->id,
                'title' => $b['title'],
                'slug' => Str::slug($b['title']),
                'price' => $b['price'],
                'stock_quantity' => $b['stock'],
                'language' => $b['lang'],
                'published_date' => $b['date'],
                'description' => 'A great book about ' . $b['cat'],
                'image' => $imagePath ?? 'default.png',
                'status' => 'active',
            ]);
            $authIds = Author::whereIn('name', $b['auth'])->pluck('id')->toArray();
            $book->authors()->syncWithoutDetaching($authIds);
        }

        echo "\n✅ Done! Total books: " . Book::count() . "\n";
    }
}
