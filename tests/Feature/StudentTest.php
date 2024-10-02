<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\StudentSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StudentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function loginAs(){
        $data = [
            'email'=> 'pankaj@gmail.com',
            'password'=>12345678];

        // user Login
        $response = $this->postJson('/api/login', $data);
        $response->assertStatus(200);
    }

    public function test_index_returns_all_students()
    {

        $this->loginAs();
        // Create some students
        Student::factory()->count(5)->create();

        // Call the index method
        $response = $this->getJson('/api/students');

        // Assert the response is OK
        $response->assertStatus(200);
    }

    public function test_store_creates_a_student()
    {
        $this->loginAs();
        // Data for a new student
        $data = [
            'first_name' => 'John',
            'middle_name' => 'Doe',
            'last_name' => 'Smith',
            'date_of_birth' => '2000-01-01',
            'age' => 24,
            'gender' => 'male',
            'availability' => [
                'monday' => true,
                'tuesday' => false,
                'wednesday' => true,
                'thursday' => false,
                'friday' => true,
                'saturday' => false,
                'sunday' => true,
            ],
        ];

        // Call the store method
        $response = $this->postJson('/api/students', $data);

        // Assert the response is OK
        $response->assertStatus(201);

        // Assert the student was created
        $this->assertDatabaseHas('students', [
            'first_name' => 'John',
            'last_name' => 'Smith',
        ]);
    }
//
    public function test_store_validates_input()
    {
        $this->loginAs();
        // Call the store method with invalid data
        $response = $this->postJson('/api/students', []);

        // Assert the response contains validation errors
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['first_name', 'last_name', 'date_of_birth', 'age', 'gender']);
    }

}
