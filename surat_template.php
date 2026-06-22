<?php
// Template isi surat — meniru format surat .docx yang sudah dipakai sebelumnya
function buatIsiSuratTidakLulus($firstName, $lastName)
{
    $nama = trim($firstName . ' ' . $lastName);
    return <<<TEXT
NEW STUDENT ADMISSION COMMITTEE (SPMB)
INDEPENDENT PATHWAY FOR INTERNATIONAL STUDENTS
HEALTH POLYTECHNIC OF THE MINISTRY OF HEALTH BENGKULU

ANNOUNCEMENT OF ADMINISTRATIVE SELECTION RESULT
New Student Admission — Independent Pathway for International Students

Dear {$nama},
Candidate for International Student Admission

Dear Sir/Madam,

In connection with the New Student Admission (SPMB) through the Independent Pathway for International Students of the Health Polytechnic of the Ministry of Health Bengkulu (Politeknik Kesehatan Kementerian Kesehatan Bengkulu), we hereby inform you of the result of the administrative selection stage for the above-named applicant.

Based on the document verification and assessment carried out during the administrative selection stage, we hereby inform you that the above-named applicant has NOT PASSED the administrative selection and is therefore declared DISQUALIFIED from proceeding to the next stage of the selection.

The decision of the committee is final and cannot be contested. For further information regarding the selection results, please contact the committee through the official channels that have been provided.

We sincerely appreciate your interest and participation in this selection. We wish you continued enthusiasm and success in pursuing your aspirations in the future.

Sincerely,
International Student Admission Committee
Health Polytechnic of the Ministry of Health Bengkulu
TEXT;
}

function suratSubjectTidakLulus()
{
    return 'Announcement of Administrative Selection Result - SPMB Poltekkes Kemenkes Bengkulu';
}
