<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $testUsers = [
            [
                'name' =>'Admin',
                'role' =>1,
                'email' =>'admin@admin.com',
                'password' =>Hash::make("1234"),
                'created_at' => '2020-07-02 13:06:47',
                'updated_at' => '2020-07-02 13:06:47',
            ],
            [
                'name' =>'Teacher',
                'role' =>2,
                'email' =>'teacher@teacher.com',
                'password' =>Hash::make("1234"),
                'created_at' => '2020-07-02 13:06:47',
                'updated_at' => '2020-07-02 13:06:47',
            ],
            [
                'name' =>'Student',
                'role' =>3,
                'email' =>'student@student.com',
                'password' =>Hash::make("1234"),
                'created_at' => '2020-07-02 13:06:47',
                'updated_at' => '2020-07-02 13:06:47',
            ],
        ];

        DB::table("users")->insert($testUsers);

        factory(\App\User::class, 1000)->create();
        factory(\App\ClassUser::class, 1000)->create();

        $testRoles = [
            [
                'id' =>1,
                'name' =>"admin",
            ],
            [
                'id' =>2,
                'name' =>"teacher",
            ],
            [
                'id' =>3,
                'name' =>"student",
            ],

        ];

        DB::table("roles")->insert($testRoles);

        $testCampus = [
            [
                'campus_name' =>"Kadıköy Kampus",
                'campus_code' =>"KKC",
            ],
            [
                'campus_name' =>"Beşiktaş Kampus",
                'campus_code' =>"BSKC",
            ],
            [
                'campus_name' =>"Pendik Kampus",
                'campus_code' =>"PNDC",
            ],

        ];

        DB::table("campuses")->insert($testCampus);

        $testClass = [
            [
                'class_name' =>"1-A",
                'class_level' => 1,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"1-B",
                'class_level' => 1,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"1-C",
                'class_level' => 1,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"1-D",
                'class_level' => 1,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"2-A",
                'class_level' => 2,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"2-B",
                'class_level' => 2,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"2-C",
                'class_level' => 2,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"2-D",
                'class_level' => 2,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"3-A",
                'class_level' => 3,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"3-B",
                'class_level' => 3,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"3-C",
                'class_level' => 3,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"3-D",
                'class_level' => 3,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"4-A",
                'class_level' => 4,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"4-B",
                'class_level' => 4,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"4-C",
                'class_level' => 4,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"4-D",
                'class_level' => 4,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"5-A",
                'class_level' => 5,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"5-B",
                'class_level' => 5,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"5-C",
                'class_level' => 5,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"5-D",
                'class_level' => 5,
                'class_campus_id' => 1,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"Atatürkçülük",
                'class_level' => 5,
                'class_campus_id' => 1,
                'is_class_mixed' => 1,
            ],
            [
                'class_name' =>"İtalyanca",
                'class_level' => 5,
                'class_campus_id' => 1,
                'is_class_mixed' => 1,
            ],
            [
                'class_name' =>"Fransızca",
                'class_level' => 5,
                'class_campus_id' => 1,
                'is_class_mixed' => 1,
            ],
            [
                'class_name' =>"1-A",
                'class_level' => 1,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"1-B",
                'class_level' => 1,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"1-C",
                'class_level' => 1,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"1-D",
                'class_level' => 1,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"2-A",
                'class_level' => 2,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"2-B",
                'class_level' => 2,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"2-C",
                'class_level' => 2,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"2-D",
                'class_level' => 2,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"3-A",
                'class_level' => 3,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"3-B",
                'class_level' => 3,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"3-C",
                'class_level' => 3,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"3-D",
                'class_level' => 3,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"4-A",
                'class_level' => 4,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"4-B",
                'class_level' => 4,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"4-C",
                'class_level' => 4,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"4-D",
                'class_level' => 4,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"5-A",
                'class_level' => 5,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"5-B",
                'class_level' => 5,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"5-C",
                'class_level' => 5,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"5-D",
                'class_level' => 5,
                'class_campus_id' => 2,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"Atatürkçülük",
                'class_level' => 5,
                'class_campus_id' => 2,
                'is_class_mixed' => 1,
            ],
            [
                'class_name' =>"İtalyanca",
                'class_level' => 5,
                'class_campus_id' => 2,
                'is_class_mixed' => 1,
            ],
            [
                'class_name' =>"Fransızca",
                'class_level' => 5,
                'class_campus_id' => 2,
                'is_class_mixed' => 1,
            ],
            [
                'class_name' =>"1-A",
                'class_level' => 1,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"1-B",
                'class_level' => 1,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"1-C",
                'class_level' => 1,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"1-D",
                'class_level' => 1,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"2-A",
                'class_level' => 2,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"2-B",
                'class_level' => 2,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"2-C",
                'class_level' => 2,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"2-D",
                'class_level' => 2,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"3-A",
                'class_level' => 3,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"3-B",
                'class_level' => 3,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"3-C",
                'class_level' => 3,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"3-D",
                'class_level' => 3,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"4-A",
                'class_level' => 4,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"4-B",
                'class_level' => 4,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"4-C",
                'class_level' => 4,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"4-D",
                'class_level' => 4,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"5-A",
                'class_level' => 5,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"5-B",
                'class_level' => 5,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"5-C",
                'class_level' => 5,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"5-D",
                'class_level' => 5,
                'class_campus_id' => 3,
                'is_class_mixed' => 0,
            ],
            [
                'class_name' =>"Atatürkçülük",
                'class_level' => 5,
                'class_campus_id' => 3,
                'is_class_mixed' => 1,
            ],
            [
                'class_name' =>"İtalyanca",
                'class_level' => 5,
                'class_campus_id' => 3,
                'is_class_mixed' => 1,
            ],
            [
                'class_name' =>"Fransızca",
                'class_level' => 5,
                'class_campus_id' => 3,
                'is_class_mixed' => 1,
            ],
        ];

        DB::table("clsses")->insert($testClass);

        $testCourses = [
            [
                'course_name' =>"Türkçe",
                'course_level' => 1,
                'course_code' => 'TR01',
            ],
            [
                'course_name' =>"Türkçe",
                'course_level' => 2,
                'course_code' => 'TR02',
            ],
            [
                'course_name' =>"Türkçe",
                'course_level' => 3,
                'course_code' => 'TR03',
            ],
            [
                'course_name' =>"Türkçe",
                'course_level' => 4,
                'course_code' => 'TR04',
            ],
            [
                'course_name' =>"Türkçe",
                'course_level' => 5,
                'course_code' => 'TR05',
            ],
            [
                'course_name' =>"Türkçe",
                'course_level' => 6,
                'course_code' => 'TR06',
            ],
            [
                'course_name' =>"Türkçe",
                'course_level' => 7,
                'course_code' => 'TR07',
            ],
            [
                'course_name' =>"Türkçe",
                'course_level' => 8,
                'course_code' => 'TR08',
            ],
            [
                'course_name' =>"Matematik",
                'course_level' => 1,
                'course_code' => 'MT01',
            ],
            [
                'course_name' =>"Matematik",
                'course_level' => 2,
                'course_code' => 'MT02',
            ],
            [
                'course_name' =>"Matematik",
                'course_level' => 3,
                'course_code' => 'MT03',
            ],
            [
                'course_name' =>"Matematik",
                'course_level' => 4,
                'course_code' => 'MT04',
            ],
            [
                'course_name' =>"Matematik",
                'course_level' => 5,
                'course_code' => 'MT05',
            ],
            [
                'course_name' =>"Matematik",
                'course_level' => 6,
                'course_code' => 'MT06',
            ],
            [
                'course_name' =>"Matematik",
                'course_level' => 7,
                'course_code' => 'MT07',
            ],
            [
                'course_name' =>"Matematik",
                'course_level' => 8,
                'course_code' => 'MT08',
            ],
            [
                'course_name' =>"Sosyal Bilgiler",
                'course_level' => 1,
                'course_code' => 'SOS01',
            ],
            [
                'course_name' =>"Sosyal Bilgiler",
                'course_level' => 2,
                'course_code' => 'SOS02',
            ],
            [
                'course_name' =>"Sosyal Bilgiler",
                'course_level' => 3,
                'course_code' => 'SOS03',
            ],
            [
                'course_name' =>"Sosyal Bilgiler",
                'course_level' => 4,
                'course_code' => 'SOS04',
            ],
            [
                'course_name' =>"Sosyal Bilgiler",
                'course_level' => 5,
                'course_code' => 'SOS05',
            ],
            [
                'course_name' =>"Sosyal Bilgiler",
                'course_level' => 6,
                'course_code' => 'SOS06',
            ],
            [
                'course_name' =>"Sosyal Bilgiler",
                'course_level' => 7,
                'course_code' => 'SOS07',
            ],
            [
                'course_name' =>"Sosyal Bilgiler",
                'course_level' => 8,
                'course_code' => 'SOS08',
            ],
        ];

        DB::table("courses")->insert($testCourses);

        $testLectures = [
            [
                'lecture_name' =>"Zarflar",
                'lecture_level' => 1,
                'lecture_course_id' => 1,
                'parent_lecture_id' => 0,
            ],
            [
                'lecture_name' =>"Zaman Zarfları",
                'lecture_level' => 1,
                'lecture_course_id' => 1,
                'parent_lecture_id' => 1,
            ],
            [
                'lecture_name' =>"Yön Zarfları",
                'lecture_level' => 1,
                'lecture_course_id' => 1,
                'parent_lecture_id' => 1,
            ],
            [
                'lecture_name' =>"Miktar Zarfları",
                'lecture_level' => 1,
                'lecture_course_id' => 1,
                'parent_lecture_id' => 1,
            ],
            [
                'lecture_name' =>"Gösterme Zarfı",
                'lecture_level' => 1,
                'lecture_course_id' => 1,
                'parent_lecture_id' => 1,
            ],

            [
                'lecture_name' =>"Zamirler",
                'lecture_level' => 1,
                'lecture_course_id' => 1,
                'parent_lecture_id' => 0,
            ],
            [
                'lecture_name' =>"Şahıs Zamirleri",
                'lecture_level' => 1,
                'lecture_course_id' => 1,
                'parent_lecture_id' => 6,
            ],
            [
                'lecture_name' =>"Dönüşlülük zamiri",
                'lecture_level' => 1,
                'lecture_course_id' => 1,
                'parent_lecture_id' => 6,
            ],
            [
                'lecture_name' =>"İşaret zamirleri",
                'lecture_level' => 1,
                'lecture_course_id' => 1,
                'parent_lecture_id' => 6,
            ],
            [
                'lecture_name' =>"Belgisiz zamirler",
                'lecture_level' => 1,
                'lecture_course_id' => 1,
                'parent_lecture_id' => 6,
            ],


        ];

        DB::table("lectures")->insert($testLectures);


        $testHomeworks = [
            [
                'homework_name' =>"Türkçe Ödevi",
                'homework_course_id' => 1,
                'homework_lecture_id' => 1,
                'homework_publisher_id' => 2,
                'homework_time' => 600,
                'is_visible_before_deadline' => 1,
            ],
            [
                'homework_name' =>"Türkçe Ödevi",
                'homework_course_id' => 1,
                'homework_lecture_id' => 1,
                'homework_publisher_id' => 2,
                'homework_time' => 600,
                'is_visible_before_deadline' => 1,
            ],
            [
                'homework_name' =>"Türkçe Ödevi",
                'homework_course_id' => 1,
                'homework_lecture_id' => 1,
                'homework_publisher_id' => 2,
                'homework_time' => 600,
                'is_visible_before_deadline' => 1,
            ],
            [
                'homework_name' =>"Türkçe Ödevi",
                'homework_course_id' => 1,
                'homework_lecture_id' => 1,
                'homework_publisher_id' => 2,
                'homework_time' => 600,
                'is_visible_before_deadline' => 1,
            ],
            [
                'homework_name' =>"Türkçe Ödevi",
                'homework_course_id' => 1,
                'homework_lecture_id' => 1,
                'homework_publisher_id' => 2,
                'homework_time' => 600,
                'is_visible_before_deadline' => 1,
            ],
            [
                'homework_name' =>"Türkçe Ödevi",
                'homework_course_id' => 1,
                'homework_lecture_id' => 1,
                'homework_publisher_id' => 2,
                'homework_time' => 600,
                'is_visible_before_deadline' => 1,
            ],
            [
                'homework_name' =>"Türkçe Ödevi",
                'homework_course_id' => 1,
                'homework_lecture_id' => 1,
                'homework_publisher_id' => 2,
                'homework_time' => 600,
                'is_visible_before_deadline' => 1,
            ],
            [
                'homework_name' =>"Türkçe Ödevi",
                'homework_course_id' => 1,
                'homework_lecture_id' => 1,
                'homework_publisher_id' => 2,
                'homework_time' => 600,
                'is_visible_before_deadline' => 1,
            ],
            [
                'homework_name' =>"Türkçe Ödevi",
                'homework_course_id' => 1,
                'homework_lecture_id' => 1,
                'homework_publisher_id' => 2,
                'homework_time' => 600,
                'is_visible_before_deadline' => 1,
            ],
            [
                'homework_name' =>"Türkçe Ödevi",
                'homework_course_id' => 1,
                'homework_lecture_id' => 1,
                'homework_publisher_id' => 2,
                'homework_time' => 600,
                'is_visible_before_deadline' => 1,
            ],
            [
                'homework_name' =>"Türkçe Ödevi",
                'homework_course_id' => 1,
                'homework_lecture_id' => 1,
                'homework_publisher_id' => 2,
                'homework_time' => 600,
                'is_visible_before_deadline' => 1,
            ],
            [
                'homework_name' =>"Türkçe Ödevi",
                'homework_course_id' => 1,
                'homework_lecture_id' => 1,
                'homework_publisher_id' => 2,
                'homework_time' => 600,
                'is_visible_before_deadline' => 1,
            ],


        ];

        DB::table("homework")->insert($testHomeworks);

        $testHomeworkUsers = [
            [
                'homework_id' => 1,
                'user_id' => 436,
                'start_time' => "2020-07-07 11:53:00",
                'deadline' => "2020-08-31 11:53:00",
                'point' => 0,
                'total_time' => 600,
            ],
            [
                'homework_id' => 2,
                'user_id' => 436,
                'start_time' => "2020-07-07 11:53:00",
                'deadline' => "2020-08-31 11:53:00",
                'point' => 0,
                'total_time' => 600,
            ],
            [
                'homework_id' => 3,
                'user_id' => 436,
                'start_time' => "2020-07-07 11:53:00",
                'deadline' => "2020-08-31 11:53:00",
                'point' => 0,
                'total_time' => 600,
            ],
            [
                'homework_id' => 4,
                'user_id' => 436,
                'start_time' => "2020-07-07 11:53:00",
                'deadline' => "2020-08-31 11:53:00",
                'point' => 0,
                'total_time' => 600,
            ],


        ];

        DB::table("homework_users")->insert($testHomeworkUsers);


        $testQuestions = [
            [
                'homework_id' => 1,
                'question_type' => 436,
                'question_index' => 1,
                'option_count' => 4,
                'answer' => 1,
                'correct_answer' => 600,
                'rating' => 600,
                'ratable' => 0,
                'is_question_pool' => 600,
            ],
            [
                'homework_id' => 1,
                'question_type' => 436,
                'question_index' => 2,
                'option_count' => 4,
                'answer' => 1,
                'correct_answer' => 600,
                'rating' => 600,
                'ratable' => 0,
                'is_question_pool' => 600,
            ],
            [
                'homework_id' => 1,
                'question_type' => 436,
                'question_index' => 3,
                'option_count' => 4,
                'answer' => 1,
                'correct_answer' => 600,
                'rating' => 600,
                'ratable' => 0,
                'is_question_pool' => 600,
            ],


        ];

        DB::table("questions")->insert($testQuestions);



        $testQuestionImages = [
            [
                'question_id' => 1,
                'image_url' => "image.jpg",
            ],
            [
                'question_id' => 2,
                'image_url' => "image.jpg",
            ],
            [
                'question_id' => 3,
                'image_url' => "image.jpg",
            ],

        ];

        DB::table("question_images")->insert($testQuestionImages);


        $testSettings = [
            [
                'name' => "allowedImageTypes",
                'value' => "jpg",
            ],
            [
                'name' => "allowedImageTypes",
                'value' => "png",
            ],
            [
                'name' => "allowedImageTypes",
                'value' => "gif",
            ],
            [
                'name' => "allowedImageTypes",
                'value' => "jpeg",
            ],
            [
                'name' => "adminCpPaginate",
                'value' => "20",
            ],


        ];


        DB::table("settings")->insert($testSettings);


    }
}


