<?php

namespace Database\Seeders;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder populating at least 20 realistic medical dummy cases.
 */
class MedicalCaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->admin()->create();

        $casesData = [
            [
                'case_number' => 'CASE-0001',
                'patient_reference' => 'REF-92810',
                'surgeon_name' => 'Dr. Marcus Vance, MD',
                'implant_type' => 'Ceramic Total Hip Arthroplasty',
                'status' => 'Planning',
                'priority' => 'High',
                'surgery_date' => '2026-10-28',
            ],
            [
                'case_number' => 'CASE-0002',
                'patient_reference' => 'REF-48291',
                'surgeon_name' => 'Dr. Elena Rostova, MD',
                'implant_type' => 'Bicruciate-Retaining Total Knee System',
                'status' => 'In Review',
                'priority' => 'Medium',
                'surgery_date' => '2026-11-04',
            ],
            [
                'case_number' => 'CASE-0003',
                'patient_reference' => 'REF-11928',
                'surgeon_name' => 'Dr. Julian Thorne, MD',
                'implant_type' => 'Custom Mandibular Reconstruction Plate',
                'status' => 'Approved',
                'priority' => 'High',
                'surgery_date' => '2026-10-22',
            ],
            [
                'case_number' => 'CASE-0004',
                'patient_reference' => 'REF-55912',
                'surgeon_name' => 'Dr. Sophia Al-Mansoor, MD',
                'implant_type' => 'Anterior Cervical Interbody Fusion Cage (PEEK)',
                'status' => 'Draft',
                'priority' => 'Low',
                'surgery_date' => null,
            ],
            [
                'case_number' => 'CASE-0005',
                'patient_reference' => 'REF-77219',
                'surgeon_name' => 'Dr. Christopher Lee, MD',
                'implant_type' => 'Reverse Total Shoulder Arthroplasty',
                'status' => 'Completed',
                'priority' => 'High',
                'surgery_date' => '2026-09-15',
            ],
            [
                'case_number' => 'CASE-0006',
                'patient_reference' => 'REF-30291',
                'surgeon_name' => 'Dr. Rachel Patel, MD',
                'implant_type' => 'Thoracolumbar Pedicle Screw System',
                'status' => 'Planning',
                'priority' => 'Medium',
                'surgery_date' => '2026-11-12',
            ],
            [
                'case_number' => 'CASE-0007',
                'patient_reference' => 'REF-84729',
                'surgeon_name' => 'Dr. Arthur Pendelton, MD',
                'implant_type' => 'Custom Titanium Cranial Mesh',
                'status' => 'Approved',
                'priority' => 'High',
                'surgery_date' => '2026-10-30',
            ],
            [
                'case_number' => 'CASE-0008',
                'patient_reference' => 'REF-29384',
                'surgeon_name' => 'Dr. Marcus Vance, MD',
                'implant_type' => 'Cementless Femoral Hip Stem',
                'status' => 'Completed',
                'priority' => 'Medium',
                'surgery_date' => '2026-09-28',
            ],
            [
                'case_number' => 'CASE-0009',
                'patient_reference' => 'REF-64820',
                'surgeon_name' => 'Dr. Elena Rostova, MD',
                'implant_type' => 'Anatomical Radial Head Prosthesis',
                'status' => 'Draft',
                'priority' => 'Low',
                'surgery_date' => null,
            ],
            [
                'case_number' => 'CASE-0010',
                'patient_reference' => 'REF-91823',
                'surgeon_name' => 'Dr. Julian Thorne, MD',
                'implant_type' => 'Bioabsorbable Interference Screw Set',
                'status' => 'In Review',
                'priority' => 'Low',
                'surgery_date' => '2026-11-25',
            ],
            [
                'case_number' => 'CASE-0011',
                'patient_reference' => 'REF-12398',
                'surgeon_name' => 'Dr. Rachel Patel, MD',
                'implant_type' => 'Patient-Specific Osteotomy Guide & Plate',
                'status' => 'Approved',
                'priority' => 'High',
                'surgery_date' => '2026-11-02',
            ],
            [
                'case_number' => 'CASE-0012',
                'patient_reference' => 'REF-48204',
                'surgeon_name' => 'Dr. Christopher Lee, MD',
                'implant_type' => 'Cobalt-Chrome Femoral Condyle',
                'status' => 'Completed',
                'priority' => 'Medium',
                'surgery_date' => '2026-09-08',
            ],
            [
                'case_number' => 'CASE-0013',
                'patient_reference' => 'REF-83921',
                'surgeon_name' => 'Dr. Sophia Al-Mansoor, MD',
                'implant_type' => 'Dynamic Hip Screw (DHS) Plate System',
                'status' => 'Planning',
                'priority' => 'High',
                'surgery_date' => '2026-11-19',
            ],
            [
                'case_number' => 'CASE-0014',
                'patient_reference' => 'REF-50294',
                'surgeon_name' => 'Dr. Arthur Pendelton, MD',
                'implant_type' => 'Zygomatic Orbital Reconstruction Implant',
                'status' => 'Cancelled',
                'priority' => 'Low',
                'surgery_date' => null,
            ],
            [
                'case_number' => 'CASE-0015',
                'patient_reference' => 'REF-68201',
                'surgeon_name' => 'Dr. Marcus Vance, MD',
                'implant_type' => 'Dual Mobility Acetabular Cup System',
                'status' => 'In Review',
                'priority' => 'Medium',
                'surgery_date' => '2026-12-01',
            ],
            [
                'case_number' => 'CASE-0016',
                'patient_reference' => 'REF-74920',
                'surgeon_name' => 'Dr. Elena Rostova, MD',
                'implant_type' => 'Unicompartmental Knee Arthroplasty (UKA)',
                'status' => 'Approved',
                'priority' => 'Medium',
                'surgery_date' => '2026-11-08',
            ],
            [
                'case_number' => 'CASE-0017',
                'patient_reference' => 'REF-99201',
                'surgeon_name' => 'Dr. Julian Thorne, MD',
                'implant_type' => 'Titanium Sternotomy Cable & Plate Fixation',
                'status' => 'Planning',
                'priority' => 'High',
                'surgery_date' => '2026-11-16',
            ],
            [
                'case_number' => 'CASE-0018',
                'patient_reference' => 'REF-38291',
                'surgeon_name' => 'Dr. Rachel Patel, MD',
                'implant_type' => 'Lumbar Disc Replacement Prosthesis',
                'status' => 'Completed',
                'priority' => 'High',
                'surgery_date' => '2026-10-02',
            ],
            [
                'case_number' => 'CASE-0019',
                'patient_reference' => 'REF-17294',
                'surgeon_name' => 'Dr. Christopher Lee, MD',
                'implant_type' => 'Distal Radius Volar Locking Plate',
                'status' => 'Draft',
                'priority' => 'Low',
                'surgery_date' => null,
            ],
            [
                'case_number' => 'CASE-0020',
                'patient_reference' => 'REF-88291',
                'surgeon_name' => 'Dr. Sophia Al-Mansoor, MD',
                'implant_type' => 'Custom Pelvic Hemipelvectomy Cage',
                'status' => 'Approved',
                'priority' => 'High',
                'surgery_date' => '2026-11-22',
            ],
            [
                'case_number' => 'CASE-0021',
                'patient_reference' => 'REF-62941',
                'surgeon_name' => 'Dr. Arthur Pendelton, MD',
                'implant_type' => 'Intramedullary Femoral Reconstruction Nail',
                'status' => 'Planning',
                'priority' => 'Medium',
                'surgery_date' => '2026-12-05',
            ],
            [
                'case_number' => 'CASE-0022',
                'patient_reference' => 'REF-49201',
                'surgeon_name' => 'Dr. Marcus Vance, MD',
                'implant_type' => 'Calcaneal Fracture Locking Mesh Plate',
                'status' => 'In Review',
                'priority' => 'Low',
                'surgery_date' => '2026-12-10',
            ],
        ];

        foreach ($casesData as $caseItem) {
            $caseItem['created_by'] = $admin->id;
            MedicalCase::updateOrCreate(
                ['case_number' => $caseItem['case_number']],
                $caseItem
            );
        }
    }
}
