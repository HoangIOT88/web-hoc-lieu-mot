<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Course;
use App\Models\Lecture;
use App\Models\Exercise;
use App\Models\ChatGroup;
use App\Models\ChatGroupMember;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'ADMIN',
        ]);
        
        // Create content users (course managers)
        $contentUser1 = User::create([
            'name' => 'Content Manager 1',
            'email' => 'content1@example.com',
            'password' => Hash::make('password'),
            'role' => 'CONTENT_USER',
        ]);
        
        $contentUser2 = User::create([
            'name' => 'Content Manager 2',
            'email' => 'content2@example.com',
            'password' => Hash::make('password'),
            'role' => 'CONTENT_USER',
        ]);
        
        // Create regular users (students)
        $regularUsers = [];
        for ($i = 1; $i <= 5; $i++) {
            $regularUsers[] = User::create([
                'name' => "Student $i",
                'email' => "student$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'USER',
            ]);
        }
        
        // Create sample courses
        $course1 = Course::create([
            'name' => 'Introduction to Programming',
            'description' => 'A beginner-friendly course covering the basics of programming.',
            'duration' => '8 weeks',
            'content_user_id' => $contentUser1->id,
        ]);
        
        $course2 = Course::create([
            'name' => 'Advanced Web Development',
            'description' => 'Learn modern web development techniques and frameworks.',
            'duration' => '12 weeks',
            'content_user_id' => $contentUser1->id,
        ]);
        
        $course3 = Course::create([
            'name' => 'Data Science Fundamentals',
            'description' => 'An introduction to data science, analytics, and visualization.',
            'duration' => '10 weeks',
            'content_user_id' => $contentUser2->id,
        ]);
        
        // Create sample lectures for courses
        Lecture::create([
            'course_id' => $course1->id,
            'title' => 'Getting Started with Programming',
            'description' => 'Learn the basics of programming concepts.',
            'file_url' => 'lectures/intro_programming_01.pdf',
        ]);
        
        Lecture::create([
            'course_id' => $course1->id,
            'title' => 'Variables and Data Types',
            'description' => 'Understanding variables and data types in programming.',
            'file_url' => 'lectures/intro_programming_02.pdf',
        ]);
        
        Lecture::create([
            'course_id' => $course2->id,
            'title' => 'Modern JavaScript Frameworks',
            'description' => 'An overview of popular JavaScript frameworks.',
            'file_url' => 'lectures/web_dev_01.pdf',
        ]);
        
        Lecture::create([
            'course_id' => $course3->id,
            'title' => 'Introduction to Data Analysis',
            'description' => 'Basic concepts in data analysis.',
            'file_url' => 'lectures/data_science_01.pdf',
        ]);
        
        // Create sample exercises
        Exercise::create([
            'course_id' => $course1->id,
            'title' => 'Hello World Program',
            'content' => 'Write a simple program that prints "Hello, World!" to the console.',
            'deadline' => now()->addWeeks(1),
        ]);
        
        Exercise::create([
            'course_id' => $course1->id,
            'title' => 'Working with Variables',
            'content' => 'Create variables of different data types and perform operations on them.',
            'deadline' => now()->addWeeks(2),
        ]);
        
        Exercise::create([
            'course_id' => $course2->id,
            'title' => 'Building a Simple React Component',
            'content' => 'Create a functional React component that displays user information.',
            'deadline' => now()->addWeeks(1),
        ]);
        
        Exercise::create([
            'course_id' => $course3->id,
            'title' => 'Data Cleaning and Preparation',
            'content' => 'Clean and prepare a dataset for analysis.',
            'deadline' => now()->addWeeks(1),
        ]);
        
        // Register some users for courses
        $course1->registeredUsers()->attach([
            $regularUsers[0]->id => ['registered_at' => now()],
            $regularUsers[1]->id => ['registered_at' => now()],
            $regularUsers[2]->id => ['registered_at' => now()],
        ]);
        
        $course2->registeredUsers()->attach([
            $regularUsers[1]->id => ['registered_at' => now()],
            $regularUsers[3]->id => ['registered_at' => now()],
        ]);
        
        $course3->registeredUsers()->attach([
            $regularUsers[2]->id => ['registered_at' => now()],
            $regularUsers[4]->id => ['registered_at' => now()],
        ]);
        
        // Create chat groups
        $generalChatGroup = ChatGroup::create([
            'name' => 'General Discussion',
            'description' => 'A place for general discussion about programming.',
            'created_by' => $contentUser1->id,
        ]);
        
        $course1ChatGroup = ChatGroup::create([
            'name' => 'Intro to Programming Group',
            'description' => 'Discussion group for Introduction to Programming course.',
            'created_by' => $contentUser1->id,
        ]);
        
        $course2ChatGroup = ChatGroup::create([
            'name' => 'Web Development Group',
            'description' => 'Discussion group for Advanced Web Development course.',
            'created_by' => $contentUser1->id,
        ]);
        
        // Add members to chat groups
        $generalChatGroup->members()->attach([
            $contentUser1->id => ['joined_at' => now()],
            $contentUser2->id => ['joined_at' => now()],
            $regularUsers[0]->id => ['joined_at' => now()],
            $regularUsers[1]->id => ['joined_at' => now()],
            $regularUsers[2]->id => ['joined_at' => now()],
            $regularUsers[3]->id => ['joined_at' => now()],
            $regularUsers[4]->id => ['joined_at' => now()],
        ]);
        
        $course1ChatGroup->members()->attach([
            $contentUser1->id => ['joined_at' => now()],
            $regularUsers[0]->id => ['joined_at' => now()],
            $regularUsers[1]->id => ['joined_at' => now()],
            $regularUsers[2]->id => ['joined_at' => now()],
        ]);
        
        $course2ChatGroup->members()->attach([
            $contentUser1->id => ['joined_at' => now()],
            $regularUsers[1]->id => ['joined_at' => now()],
            $regularUsers[3]->id => ['joined_at' => now()],
        ]);
    }
}
