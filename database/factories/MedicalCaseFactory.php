<?php

namespace Database\Factories;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalCase>
 */
class MedicalCaseFactory extends Factory
{
    protected $model = MedicalCase::class;

    /**
     * Common surgical implant types manufactured or tracked by orthopedic & medical device firms.
     */
    private static array $implantTypes = [
        'Total Hip Arthroplasty (Ceramic-on-Polyethylene)',
        'Cementless Femoral Hip Stem (Titanium Porous)',
        'Bicruciate-Retaining Total Knee System',
        'Custom 3D-Printed Mandibular Reconstruction Plate',
        'Anterior Cervical Interbody Fusion Cage (PEEK)',
        'Thoracolumbar Spinal Fixation Pedicle Screw System',
        'Reverse Total Shoulder Arthroplasty',
        'Anatomical Radial Head Prosthesis',
        'Bioabsorbable Suture Anchor System',
        'Custom Cranial Reconstruction Mesh (Titanium Grade 5)',
    ];

    /**
     * Define the model's default state with realistic dummy data.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $caseSequence = 1000;
        $caseSequence++;

        return [
            'case_number' => 'CASE-' . str_pad((string)$caseSequence, 4, '0', STR_PAD_LEFT),
            'patient_reference' => 'REF-' . fake()->unique()->numerify('#####'),
            'surgeon_name' => 'Dr. ' . fake()->firstName() . ' ' . fake()->lastName() . ', MD',
            'implant_type' => fake()->randomElement(self::$implantTypes),
            'status' => fake()->randomElement(MedicalCase::STATUSES),
            'priority' => fake()->randomElement(MedicalCase::PRIORITIES),
            'surgery_date' => fake()->boolean(85) ? fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d') : null,
            'created_by' => User::factory(),
        ];
    }
}
