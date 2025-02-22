<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ForumSeeder extends Seeder
{
    public function run()
    {
        // Define categories with base names and descriptions
        $categories = [
            ['name' => 'Technology', 'description' => 'Latest tech news, gadgets, software, and innovations.'],
            ['name' => 'Science', 'description' => 'Discuss scientific discoveries, research, and the wonders of the world.'],
            ['name' => 'Sports', 'description' => 'News and discussions on various sports including football, basketball, tennis, and more.'],
            ['name' => 'Entertainment', 'description' => 'Movies, music, TV shows, and celebrity news. Everything entertainment!'],
            ['name' => 'Health & Fitness', 'description' => 'Discussions on fitness, wellness, mental health, and healthy living.'],
        ];

        // Insert categories and subcategories with threads and posts
        foreach ($categories as $categoryData) {
            // Insert base category
            $category = DB::table('categories')->insertGetId([
                'name' => $categoryData['name'],
                'description' => $categoryData['description'],
                'parent_id' => null,
                'user_id' => 1, // Assuming a generic user
                'created_at' => Carbon::now(),
                                                             'updated_at' => Carbon::now(),
            ]);

            // Define subcategories based on the base category
            $subcategories = [];
            switch ($categoryData['name']) {
                case 'Technology':
                    $subcategories = [
                        ['name' => 'Smartphones', 'description' => 'Latest advancements in mobile phones and gadgets.'],
                        ['name' => 'Laptops', 'description' => 'All about laptops and personal computers.'],
                        ['name' => 'AI & Robotics', 'description' => 'Discuss AI and robotics technology.'],
                        ['name' => 'Software Development', 'description' => 'Topics related to software engineering and programming.'],
                        ['name' => 'Gaming', 'description' => 'Discussions on video games, consoles, and gaming culture.'],
                    ];
                    break;
                case 'Science':
                    $subcategories = [
                        ['name' => 'Astronomy', 'description' => 'Exploring the universe and celestial bodies.'],
                        ['name' => 'Biology', 'description' => 'The study of living organisms and ecosystems.'],
                        ['name' => 'Physics', 'description' => 'The science of matter and energy.'],
                        ['name' => 'Chemistry', 'description' => 'The science of substances and their reactions.'],
                        ['name' => 'Environmental Science', 'description' => 'Exploring environmental challenges and solutions.'],
                    ];
                    break;
                case 'Sports':
                    $subcategories = [
                        ['name' => 'Football', 'description' => 'Discuss all things football – from leagues to players.'],
                        ['name' => 'Basketball', 'description' => 'Everything about basketball, the NBA, and other leagues.'],
                        ['name' => 'Tennis', 'description' => 'News and discussions on tennis tournaments and players.'],
                        ['name' => 'Formula 1', 'description' => 'All about Formula 1 racing, teams, and technology.'],
                        ['name' => 'eSports', 'description' => 'Competitive gaming and the rise of esports.'],
                    ];
                    break;
                case 'Entertainment':
                    $subcategories = [
                        ['name' => 'Movies', 'description' => 'All about the latest and classic movies.'],
                        ['name' => 'TV Shows', 'description' => 'Discuss popular TV shows and series.'],
                        ['name' => 'Music', 'description' => 'Talk about music genres, artists, and albums.'],
                        ['name' => 'Celebrity News', 'description' => 'Everything happening with your favorite celebrities.'],
                        ['name' => 'Theater', 'description' => 'Discussion on live performances and theater arts.'],
                    ];
                    break;
                case 'Health & Fitness':
                    $subcategories = [
                        ['name' => 'Nutrition', 'description' => 'Tips on healthy eating, dieting, and nutrition.'],
                        ['name' => 'Exercise', 'description' => 'Workouts, exercises, and fitness tips.'],
                        ['name' => 'Mental Health', 'description' => 'Discussions on mental health, stress, and well-being.'],
                        ['name' => 'Yoga & Meditation', 'description' => 'Yoga practices, mindfulness, and meditation techniques.'],
                        ['name' => 'Weight Loss', 'description' => 'Effective strategies for losing weight and staying fit.'],
                    ];
                    break;
            }

            // Insert subcategories for each category
            foreach ($subcategories as $subcategoryData) {
                $subcategory = DB::table('categories')->insertGetId([
                    'name' => $subcategoryData['name'],
                    'description' => $subcategoryData['description'],
                    'parent_id' => $category,
                    'user_id' => 1,
                    'created_at' => Carbon::now(),
                                                                    'updated_at' => Carbon::now(),
                ]);

                // Define threads for each subcategory
                $threads = [];
                switch ($subcategoryData['name']) {
                    case 'Smartphones':
                        $threads = [
                            ['title' => 'Best Smartphones of 2025', 'content' => 'A guide to the top smartphones of 2025.'],
                            ['title' => 'Is 5G Really Worth It?', 'content' => 'A deep dive into 5G technology and its real-world applications.'],
                            ['title' => 'iPhone 15 Review: Is It Worth the Hype?', 'content' => 'Let’s break down the features and performance of the new iPhone.'],
                            ['title' => 'Android vs iPhone: Which One is Better?', 'content' => 'Comparing the pros and cons of Android and iPhone.'],
                            ['title' => 'Top Budget Smartphones for 2025', 'content' => 'Affordable yet feature-packed smartphones you should consider.'],
                        ];
                        break;
                    case 'Laptops':
                        $threads = [
                            ['title' => 'Best Laptops for Students in 2025', 'content' => 'Find the best laptops suited for students in 2025.'],
                            ['title' => 'MacBook Pro M2 vs Dell XPS 15', 'content' => 'A detailed comparison between MacBook Pro M2 and Dell XPS 15.'],
                            ['title' => 'Top Gaming Laptops in 2025', 'content' => 'Our recommendations for the best gaming laptops this year.'],
                            ['title' => 'The Best Laptops for Developers', 'content' => 'Which laptops should developers consider for coding and programming?'],
                            ['title' => 'Should You Buy a Laptop or a Desktop?', 'content' => 'Deciding between a portable laptop and a desktop setup.'],
                        ];
                        break;
                    case 'AI & Robotics':
                        $threads = [
                            ['title' => 'The Future of Artificial Intelligence', 'content' => 'What is the future of AI and how will it impact industries?'],
                            ['title' => 'How AI is Transforming Healthcare', 'content' => 'AI’s influence on the healthcare industry and patient care.'],
                            ['title' => 'Robot-Assisted Surgery: The Pros and Cons', 'content' => 'Exploring the advantages and challenges of robot-assisted surgery.'],
                            ['title' => 'The Impact of AI on Jobs', 'content' => 'How AI will change the job market and work environment.'],
                            ['title' => 'AI Ethics: Should Machines Make Decisions?', 'content' => 'A discussion on whether machines should be allowed to make critical decisions.'],
                        ];
                        break;
                    case 'Software Development':
                        $threads = [
                            ['title' => 'Best Programming Languages to Learn in 2025', 'content' => 'A guide to programming languages you should focus on.'],
                            ['title' => 'Why Git Is Essential for Developers', 'content' => 'Understanding the importance of Git for collaboration in development.'],
                            ['title' => 'The Rise of Low-Code Development', 'content' => 'What is low-code development and why is it becoming popular?'],
                            ['title' => 'Cloud Computing: What Developers Need to Know', 'content' => 'Exploring the role of cloud computing in software development.'],
                            ['title' => 'Why Every Developer Needs to Learn SQL', 'content' => 'The importance of SQL in modern software development.'],
                        ];
                        break;
                    case 'Gaming':
                        $threads = [
                            ['title' => 'Top 5 Upcoming Games in 2025', 'content' => 'A rundown of the most anticipated video games coming in 2025.'],
                            ['title' => 'PC Gaming vs Console Gaming: Which Is Better?', 'content' => 'A deep dive into PC gaming vs console gaming debates.'],
                            ['title' => 'The Rise of VR Gaming', 'content' => 'Exploring the future of virtual reality in the gaming world.'],
                            ['title' => 'eSports: The Growing Industry', 'content' => 'Understanding the rapid growth and impact of eSports in gaming.'],
                            ['title' => 'How to Build the Ultimate Gaming PC', 'content' => 'A step-by-step guide to building your dream gaming PC.'],
                        ];
                        break;
                }

                // Insert threads and posts for each subcategory
                foreach ($threads as $threadData) {
                    $thread = DB::table('threads')->insertGetId([
                        'category_id' => $subcategory,
                        'title' => $threadData['title'],
                        'content' => $threadData['content'],
                        'user_id' => 1,
                        'approved' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    // Insert one unique post for each thread
                    DB::table('posts')->insert([
                        'thread_id' => $thread,
                        'content' => 'This is my opinion on the topic. I believe the future of this technology looks promising.',
                        'user_id' => 1,
                        'approved' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }
        }
    }
}
