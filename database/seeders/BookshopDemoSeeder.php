<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Genre;
use App\Models\Category;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Support\Str;

class BookshopDemoSeeder extends Seeder
{
    public function run(): void
    {
        // ========== COUNTRIES ==========
        $countriesList = [
            ['name' => 'United States', 'code' => 'US'],
            ['name' => 'Israel', 'code' => 'IL'],
            ['name' => 'United Kingdom', 'code' => 'GB'],
            ['name' => 'Myanmar', 'code' => 'MM'],
            ['name' => 'Japan', 'code' => 'JP'],
            ['name' => 'India', 'code' => 'IN'],
            ['name' => 'France', 'code' => 'FR'],
            ['name' => 'Germany', 'code' => 'DE'],
        ];
        foreach ($countriesList as $c) {
            Country::firstOrCreate(['name' => $c['name']], $c);
        }

        // ========== GENRES ==========
        $genreNames = [
            'Financial Education', 'Business', 'Psychology', 'Decision Science',
            'Programming', 'Software Engineering', 'Self Development',
            'Communication', 'Financial Investing', 'Economics',
        ];
        $genres = [];
        foreach ($genreNames as $name) {
            $genres[$name] = Genre::firstOrCreate(['name' => $name]);
        }

        // ========== CATEGORIES ==========
        $categoryNames = ['Business', 'Psychology', 'Programming', 'Financial', 'Self Development'];
        $categories = [];
        foreach ($categoryNames as $name) {
            $categories[$name] = Category::firstOrCreate(
                ['name' => $name],
                ['description' => 'Books about ' . strtolower($name), 'status' => 'active']
            );
        }

        // ========== AUTHORS & BOOKS ==========
        $authorsData = [
            [
                'name' => 'Robert T. Kiyosaki',
                'bio' => 'Robert Toru Kiyosaki is an American businessman and author, best known for his Rich Dad Poor Dad series of personal finance books. His work focuses on financial literacy, real estate investing, and entrepreneurship.',
                'country' => 'United States',
                'genres' => ['Financial Education', 'Business'],
                'books' => [
                    ['title' => 'Rich Dad Poor Dad', 'isbn' => '978-1612680194', 'price' => 24900, 'sale_price' => 19900, 'language' => 'English', 'published' => '1997-04-08', 'color' => '1E3A8A'],
                    ['title' => 'Cashflow Quadrant', 'isbn' => '978-1612680064', 'price' => 21900, 'sale_price' => null, 'language' => 'English', 'published' => '1998-01-01', 'color' => '0F766E'],
                    ['title' => 'Rich Dad\'s Guide to Investing', 'isbn' => '978-1612680088', 'price' => 25900, 'sale_price' => 22900, 'language' => 'English', 'published' => '2000-01-01', 'color' => '7C3AED'],
                    ['title' => 'Rich Dad\'s Before You Quit Your Job', 'isbn' => '978-1612680101', 'price' => 19900, 'sale_price' => null, 'language' => 'English', 'published' => '2005-01-01', 'color' => 'B91C1C'],
                    ['title' => 'Fake: Fake Money, Fake Teachers, Fake Assets', 'isbn' => '978-1612680842', 'price' => 27900, 'sale_price' => 23900, 'language' => 'English', 'published' => '2019-04-16', 'color' => 'C2410C'],
                ],
            ],
            [
                'name' => 'Daniel Kahneman',
                'bio' => 'Daniel Kahneman is an Israeli-American psychologist and economist notable for his work on the psychology of judgment and decision-making, as well as behavioral economics. He was awarded the 2002 Nobel Prize in Economic Sciences.',
                'country' => 'Israel',
                'genres' => ['Psychology', 'Decision Science'],
                'books' => [
                    ['title' => 'Thinking, Fast and Slow', 'isbn' => '978-0374533557', 'price' => 29900, 'sale_price' => 25900, 'language' => 'English', 'published' => '2011-10-25', 'color' => '0F766E'],
                    ['title' => 'Noise: A Flaw in Human Judgment', 'isbn' => '978-0316451406', 'price' => 32900, 'sale_price' => null, 'language' => 'English', 'published' => '2021-05-18', 'color' => '7C3AED'],
                    ['title' => 'Judgment Under Uncertainty', 'isbn' => '978-0521284141', 'price' => 35900, 'sale_price' => 29900, 'language' => 'English', 'published' => '1982-04-30', 'color' => '1E3A8A'],
                ],
            ],
            [
                'name' => 'Robert C. Martin',
                'bio' => 'Robert Cecil Martin, also known as Uncle Bob, is an American software engineer and author. He is best known for promoting many software design principles and for being a co-author of the Agile Manifesto.',
                'country' => 'United States',
                'genres' => ['Programming', 'Software Engineering'],
                'books' => [
                    ['title' => 'Clean Code', 'isbn' => '978-0132350884', 'price' => 34900, 'sale_price' => 29900, 'language' => 'English', 'published' => '2008-08-01', 'color' => 'B91C1C'],
                    ['title' => 'Clean Architecture', 'isbn' => '978-0134494166', 'price' => 32900, 'sale_price' => null, 'language' => 'English', 'published' => '2017-09-10', 'color' => '1E3A8A'],
                    ['title' => 'The Clean Coder', 'isbn' => '978-0137081073', 'price' => 29900, 'sale_price' => 25900, 'language' => 'English', 'published' => '2011-05-13', 'color' => '0F766E'],
                    ['title' => 'Agile Software Development', 'isbn' => '978-0135974445', 'price' => 39900, 'sale_price' => null, 'language' => 'English', 'published' => '2002-10-21', 'color' => '7C3AED'],
                    ['title' => 'Clean Agile', 'isbn' => '978-0135781869', 'price' => 27900, 'sale_price' => 23900, 'language' => 'English', 'published' => '2019-10-14', 'color' => 'C2410C'],
                ],
            ],
            [
                'name' => 'Dale Carnegie',
                'bio' => 'Dale Carnegie was an American writer and lecturer, and the developer of courses in self-improvement, salesmanship, corporate training, public speaking, and interpersonal skills.',
                'country' => 'United States',
                'genres' => ['Self Development', 'Communication', 'Business'],
                'books' => [
                    ['title' => 'How to Win Friends and Influence People', 'isbn' => '978-0671027032', 'price' => 19900, 'sale_price' => 15900, 'language' => 'English', 'published' => '1936-10-01', 'color' => 'C2410C'],
                    ['title' => 'How to Stop Worrying and Start Living', 'isbn' => '978-0671733353', 'price' => 17900, 'sale_price' => null, 'language' => 'English', 'published' => '1948-01-01', 'color' => '1E3A8A'],
                    ['title' => 'The Quick and Easy Way to Effective Speaking', 'isbn' => '978-0671724009', 'price' => 16900, 'sale_price' => 14900, 'language' => 'English', 'published' => '1962-01-01', 'color' => '0F766E'],
                    ['title' => 'Lincoln the Unknown', 'isbn' => '978-0517415115', 'price' => 15900, 'sale_price' => null, 'language' => 'English', 'published' => '1932-01-01', 'color' => '7C3AED'],
                    ['title' => 'Public Speaking for Success', 'isbn' => '978-0061741883', 'price' => 18900, 'sale_price' => 15900, 'language' => 'English', 'published' => '2005-01-01', 'color' => 'B91C1C'],
                ],
            ],
            [
                'name' => 'Benjamin Graham',
                'bio' => 'Benjamin Graham was a British-born American economist, professor and investor. He is widely known as the father of value investing and wrote two of the most influential investment books ever published.',
                'country' => 'United States',
                'genres' => ['Financial Investing', 'Business', 'Economics'],
                'books' => [
                    ['title' => 'The Intelligent Investor', 'isbn' => '978-0060555665', 'price' => 28900, 'sale_price' => 24900, 'language' => 'English', 'published' => '1949-01-01', 'color' => '1E3A8A'],
                    ['title' => 'Security Analysis', 'isbn' => '978-0071592536', 'price' => 39900, 'sale_price' => null, 'language' => 'English', 'published' => '1934-01-01', 'color' => '0F766E'],
                    ['title' => 'The Interpretation of Financial Statements', 'isbn' => '978-0887309137', 'price' => 19900, 'sale_price' => 16900, 'language' => 'English', 'published' => '1937-01-01', 'color' => '7C3AED'],
                    ['title' => 'Storage and Stability', 'isbn' => '978-0070245839', 'price' => 24900, 'sale_price' => null, 'language' => 'English', 'published' => '1937-01-01', 'color' => 'B91C1C'],
                    ['title' => 'World Commodities and World Currency', 'isbn' => '978-0070245830', 'price' => 26900, 'sale_price' => 22900, 'language' => 'English', 'published' => '1944-01-01', 'color' => 'C2410C'],
                ],
            ],
        ];

        // ========== CREATE AUTHORS + BOOKS ==========
        foreach ($authorsData as $authorData) {
            $country = Country::where('name', $authorData['country'])->first();

            $author = Author::create([
                'name' => $authorData['name'],
                'bio' => $authorData['bio'],
                'image' => 'https://ui-avatars.com/api/?name=' . urlencode($authorData['name']) . '&background=1E3A8A&color=fff&size=400',
                'country_id' => $country ? $country->id : null,
                'status' => 'active',
                'sales_count' => rand(5000, 20000),
                'joined_date' => now()->subYears(rand(5, 30)),
                'website' => 'https://example.com/' . Str::slug($authorData['name']),
            ]);

            // Attach genres
            $genreIds = [];
            foreach ($authorData['genres'] as $genreName) {
                if (isset($genres[$genreName])) {
                    $genreIds[] = $genres[$genreName]->id;
                }
            }
            $author->genres()->sync($genreIds);

            // Map author to category
            $authorCategory = match ($authorData['name']) {
                'Robert T. Kiyosaki' => 'Financial',
                'Daniel Kahneman' => 'Psychology',
                'Robert C. Martin' => 'Programming',
                'Dale Carnegie' => 'Self Development',
                'Benjamin Graham' => 'Financial',
                default => 'Business',
            };

            $category = $categories[$authorCategory];

            // Create books
            foreach ($authorData['books'] as $bookData) {
                // Generate a more reliable image URL
                $imageUrl = $this->getBookImageUrl($bookData['title'], $bookData['color']);
                
                $book = Book::create([
                    'category_id' => $category->id,
                    'title' => $bookData['title'],
                    'slug' => Str::slug($bookData['title']),
                    'isbn' => $bookData['isbn'],
                    'price' => $bookData['price'],
                    'sale_price' => $bookData['sale_price'],
                    'sale_starts_at' => $bookData['sale_price'] ? now()->subDays(rand(1, 10)) : null,
                    'sale_ends_at' => $bookData['sale_price'] ? now()->addDays(rand(10, 30)) : null,
                    'stock_quantity' => rand(10, 100),
                    'language' => $bookData['language'],
                    'published_date' => $bookData['published'],
                    'description' => $this->getDescription($authorData['name'], $bookData['title']),
                    'image' => $imageUrl,
                    'status' => 'active',
                    'rating' => round(rand(38, 50) / 10, 1),
                    'rating_count' => rand(100, 2000),
                ]);

                // Attach author to book
                $book->authors()->attach($author->id);
            }
        }

        echo "\n✅ Demo data seeded successfully!\n";
        echo count($authorsData) . " authors\n";
        $totalBooks = collect($authorsData)->sum(fn($a) => count($a['books']));
        echo $totalBooks . " books\n";
    }

    private function getBookImageUrl(string $title, string $color): string
    {
        // Option 1: Use picsum.photos (shows random real photos)
        // return 'https://picsum.photos/seed/' . Str::slug($title) . '/400/560';
        
        // Option 2: Use UI Avatars (text-based, reliable)
        return 'https://ui-avatars.com/api/?name=' . urlencode($title) . 
               '&background=' . $color . 
               '&color=fff' . 
               '&size=400x560' . 
               '&font-size=0.5' . 
               '&bold=true&rounded=true';
        
        // Option 3: Use dummyimage.com (fallback)
        // return 'https://dummyimage.com/400x560/' . $color . '/FFFFFF&text=' . urlencode($title);
    }

    private function getDescription(string $author, string $title): string
    {
        $descriptions = [
            'Robert T. Kiyosaki' => [
                'Rich Dad Poor Dad' => 'Explodes the myth that you need to earn a high income to become rich. Learn how to make your money work for you and build wealth through investing, real estate, and entrepreneurship.',
                'Cashflow Quadrant' => 'This book reveals how some people work less, earn more, pay less in taxes, and learn to become financially free. Understand the four types of people who make up the world of business.',
                'Rich Dad\'s Guide to Investing' => 'What the rich invest in, that the poor and middle class do not. Learn the basic rules of investing and how to reduce investment risk.',
                'Rich Dad\'s Before You Quit Your Job' => 'The 10 real-life lessons every entrepreneur should know about building a multimillion-dollar business.',
                'Fake: Fake Money, Fake Teachers, Fake Assets' => 'How lies are making the poor and middle class poorer. A powerful guide to seeing through financial deception.',
            ],
            'Daniel Kahneman' => [
                'Thinking, Fast and Slow' => 'A groundbreaking tour of the mind that explains the two systems that drive the way we think — the fast, intuitive System 1 and the slow, deliberate System 2.',
                'Noise: A Flaw in Human Judgment' => 'Wherever there is judgment, there is noise. Yet noise is rarely discussed. This book explores the detrimental effects of noise in medicine, law, economic forecasting, and more.',
                'Judgment Under Uncertainty' => 'A comprehensive examination of how people make judgments and decisions when they are uncertain about the facts and values involved.',
            ],
            'Robert C. Martin' => [
                'Clean Code' => 'Even bad code can function. But if code isn\'t clean, it can bring a development organization to its knees. This book shows you how to write code that is readable, maintainable, and elegant.',
                'Clean Architecture' => 'A comprehensive guide to software architecture that covers principles, paradigms, and practices for building systems that stand the test of time.',
                'The Clean Coder' => 'A code of conduct for professional programmers. Learn what it means to be a true software craftsman — from handling conflict to saying no.',
                'Agile Software Development' => 'The definitive guide to agile principles, patterns, and practices — from planning and testing to refactoring and pair programming.',
                'Clean Agile' => 'A return to the core principles of Agile development, reminding us of the values that made Agile great in the first place.',
            ],
            'Dale Carnegie' => [
                'How to Win Friends and Influence People' => 'The classic guide to building relationships, becoming a better communicator, and winning people over to your way of thinking.',
                'How to Stop Worrying and Start Living' => 'Proven techniques for conquering worry, making decisions with confidence, and living a fuller, happier life.',
                'The Quick and Easy Way to Effective Speaking' => 'A practical guide to becoming a confident, engaging public speaker — whether in a boardroom or on a stage.',
                'Lincoln the Unknown' => 'A fascinating portrait of Abraham Lincoln — his struggles, his triumphs, and the principles that made him one of history\'s greatest leaders.',
                'Public Speaking for Success' => 'A complete course in public speaking, covering everything from preparation to delivery to handling Q&A sessions with poise.',
            ],
            'Benjamin Graham' => [
                'The Intelligent Investor' => 'The definitive book on value investing. Warren Buffett calls it "by far the best book on investing ever written." Learn how to develop long-term strategies and ignore market noise.',
                'Security Analysis' => 'The bible of value investing. First published in 1934, this book laid the intellectual foundation for modern investment analysis.',
                'The Interpretation of Financial Statements' => 'A concise, practical guide to reading and understanding financial statements — the key skill for any investor.',
                'Storage and Stability' => 'A deep dive into commodity markets, supply chains, and the economics of storage and distribution.',
                'World Commodities and World Currency' => 'An exploration of global commodity markets and their relationship to international currency systems.',
            ],
        ];

        return $descriptions[$author][$title] ?? 'A must-read book that delivers timeless wisdom and practical insights for readers seeking knowledge and growth.';
    }
}