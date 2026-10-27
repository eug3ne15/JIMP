<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentAndRoleSeeder extends Seeder
{
    public function run(): void
    {
        $departmentsAndRoles = [
            [
                'department' => [
                    'name' => 'Directorate of ICT',
                    'code' => 'DICT',
                    'description' => 'Oversees digital court systems, e-filing portals, network infrastructure, hardware support, and cybersecurity for all court stations nationwide.',
                    'icon' => 'monitor',
                ],
                'role' => [
                    'name' => 'Information & Communication Technology (ICT) Intern',
                    'field_of_study' => null, // HR is the one to add field of study
                    'description' => 'Support court automation, e-filing systems maintenance, hardware troubleshooting, network operations, and judicial ICT services.',
                    'requirements' => 'Currently enrolled in an accredited higher learning institution with an official recommendation letter.',
                    'status' => 'open',
                ],
            ],
            [
                'department' => [
                    'name' => 'Directorate of Legal Research & Kenya Judiciary Academy',
                    'code' => 'KJA',
                    'description' => 'Conducts comparative jurisprudence analysis, judicial benchmarking, continuing judicial education, and law library management.',
                    'icon' => 'book-open',
                ],
                'role' => [
                    'name' => 'Legal Research & Jurisprudence Intern',
                    'field_of_study' => null, // HR is the one to add field of study
                    'description' => 'Assist in comparative jurisprudence research, case law indexing, judicial benchmarking, and academy training coordination.',
                    'requirements' => 'Currently enrolled in an accredited faculty of law with an official recommendation letter.',
                    'status' => 'open',
                ],
            ],
            [
                'department' => [
                    'name' => 'Directorate of Human Resource Management & Development',
                    'code' => 'DHRM',
                    'description' => 'Administers judicial human capital, staff welfare, internship coordination, performance appraisals, and capacity building.',
                    'icon' => 'users',
                ],
                'role' => [
                    'name' => 'Human Resource Management Intern',
                    'field_of_study' => null, // HR is the one to add field of study
                    'description' => 'Support judicial human capital administration, staff welfare operations, internship placements, and training records.',
                    'requirements' => 'Currently enrolled in an accredited human resource or business administration programme.',
                    'status' => 'open',
                ],
            ],
            [
                'department' => [
                    'name' => 'Directorate of Public Affairs & Communication',
                    'code' => 'DPAC',
                    'description' => 'Spearheads media relations, public engagement, digital communication channels, public education, and judicial transparency.',
                    'icon' => 'message-square',
                ],
                'role' => [
                    'name' => 'Public Affairs & Corporate Communications Intern',
                    'field_of_study' => null, // HR is the one to add field of study
                    'description' => 'Assist in media monitoring, press releases, digital communication, photography, event coverage, and public outreach.',
                    'requirements' => 'Enrolled in an accredited communication, journalism, public relations, or media studies programme.',
                    'status' => 'open',
                ],
            ],
            [
                'department' => [
                    'name' => 'Directorate of Finance & Accounts',
                    'code' => 'DFA',
                    'description' => 'Coordinates budget preparation, court revenue reconciliation, fiscal compliance, and expenditure reporting across all court stations.',
                    'icon' => 'credit-card',
                ],
                'role' => [
                    'name' => 'Finance & Accounts Intern',
                    'field_of_study' => null, // HR is the one to add field of study
                    'description' => 'Participate in vote book maintenance, court revenue reconciliation, invoice processing, and financial expenditure audits.',
                    'requirements' => 'Enrolled in an accredited accounting, finance, commerce, or economics programme.',
                    'status' => 'open',
                ],
            ],
            [
                'department' => [
                    'name' => 'Directorate of Supply Chain Management',
                    'code' => 'DSCM',
                    'description' => 'Administers public procurement, asset management, contract tracking, and inventory logistics across the Judiciary.',
                    'icon' => 'shopping-bag',
                ],
                'role' => [
                    'name' => 'Supply Chain & Procurement Management Intern',
                    'field_of_study' => null, // HR is the one to add field of study
                    'description' => 'Assist in public procurement processes, inventory control, tender documentation, and supplier management.',
                    'requirements' => 'Enrolled in an accredited supply chain, procurement, or logistics programme.',
                    'status' => 'open',
                ],
            ],
            [
                'department' => [
                    'name' => 'Office of the Chief Registrar & Court Administration',
                    'code' => 'OCR',
                    'description' => 'Manages court registries, case tracking, cause lists, file archiving, and general day-to-day administrative court operations.',
                    'icon' => 'briefcase',
                ],
                'role' => [
                    'name' => 'Court Administration & Registry Intern',
                    'field_of_study' => null, // HR is the one to add field of study
                    'description' => 'Assist in court registry workflow, case tracking, filing system administration, cause list preparation, and client service.',
                    'requirements' => 'Enrolled in an accredited public administration, business management, or records management programme.',
                    'status' => 'open',
                ],
            ],
            [
                'department' => [
                    'name' => 'Directorate of Planning & Organizational Performance',
                    'code' => 'DPOP',
                    'description' => 'Leads strategic planning, court performance tracking, data analytics, institutional reforms, and statistical reporting.',
                    'icon' => 'activity',
                ],
                'role' => [
                    'name' => 'Planning & Organizational Performance Intern',
                    'field_of_study' => null, // HR is the one to add field of study
                    'description' => 'Support strategic planning, data collection, court performance monitoring, and institutional statistical analysis.',
                    'requirements' => 'Enrolled in an accredited economics, statistics, project management, or planning programme.',
                    'status' => 'open',
                ],
            ],
            [
                'department' => [
                    'name' => 'Commercial & Tax Division / Specialized Courts',
                    'code' => 'CTD',
                    'description' => 'Handles adjudication of commercial disputes, corporate conflicts, tax appeals, and admiralty matters.',
                    'icon' => 'scale',
                ],
                'role' => [
                    'name' => 'Commercial & Tax Disputes Legal Intern',
                    'field_of_study' => null, // HR is the one to add field of study
                    'description' => 'Support legal research in corporate disputes, commercial transactions, tax appeals, and maritime/admiralty law cases.',
                    'requirements' => 'Enrolled in an accredited law programme with keen interest in commercial, tax, or corporate jurisprudence.',
                    'status' => 'open',
                ],
            ],
        ];

        foreach ($departmentsAndRoles as $item) {
            $dept = Department::updateOrCreate(
                ['name' => $item['department']['name']],
                $item['department']
            );

            Course::updateOrCreate(
                ['name' => $item['role']['name']],
                array_merge($item['role'], ['department_id' => $dept->id])
            );
        }
    }
}
