<?php

namespace App\Services\Timetable;

use App\Models\Group;
use App\Models\Timetable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ParseTimetable
{
    protected Spreadsheet $spreadsheet;
    protected $sheet = 7;
    protected $course = 4;
    protected $institute_id = 1;

    public function __construct($filename)
    {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $this->spreadsheet = $reader->load(public_path($filename));
    }

    public function setCourse($course)
    {
        $this->course = $course;
    }

    public function setSheet($sheet)
    {
        $this->sheet = $sheet;
    }

    public function setInstituteId($institute)
    {
        $this->institute_id = $institute;
    }

    public function getSpreadsheet(): Spreadsheet
    {
        return $this->spreadsheet;
    }

    public function parseTimetable($sheet): array
    {
        $this->spreadsheet->setActiveSheetIndex($this->sheet);
        $active_sheet = $this->spreadsheet->getActiveSheet();

        $rows = [];
        foreach ($active_sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);
            $cells = [];
            foreach ($cellIterator as $cell) {

                $cells[] = in_array(($cell->getValue()), ['Дни', 'Часы']) ? null : $cell->getValue();
            }
            $rows[] = $cells;
        }
        $day = null;
        $groups = array_filter($rows[0]);
        $groups = array_values($groups);

        $timetable = [];
        foreach ($groups as $key_group => $group) {
            $timetable[$group] = [];
            foreach ($rows as $key_row => $row) {

                if ($row[0] !== null and $key_row !== 0) {
                    $day = Str::of($row[0])->replace("\n", '')->value();
                    $timetable[$group][$day] = [];
                }
                if ($row[1] !== null) {
                    $key_subject = ($key_group + 1) * 2;
                    $key_room = $key_subject + 1;
                    $subject = "$row[$key_subject] $row[$key_room] " . $rows[$key_row + 1][$key_subject];
                    $timetable[$group][$day][$row[1]] = !isset($row[$key_subject]) ? 'Нет занятия' : $subject;
                }
            }
        }

        return $timetable;

    }

    public function setTimetableToDb()
    {
        $currentYear = Carbon::now()->year;
        $timetable = $this->parseTimetable($this->sheet);

        foreach ($timetable as $group_title => $table){
            $group = Group::query()->updateOrCreate(['title' => $group_title, 'institute_id' => $this->institute_id], ['course' => $this->course]);
            foreach ($table as $date => $subject){
                $carbon_date = explode(" ",$date);
                $carbon_date = end($carbon_date);
                $date_to_db = Carbon::createFromFormat('d/m/Y',$carbon_date."/".$currentYear)->format('Y-m-d');

                $timetable = [
                    'date' => $date_to_db,
                    'group_id' => $group->id,
                ];
                foreach ($subject as $time => $table){
                    $timetable['time'] = $time;
                    $timetable['subject'] = $table;

                    Timetable::query()->updateOrCreate([
                        'date' => $timetable['date'],
                        'time' => $timetable['time'],
                        'group_id' => $timetable['group_id'],
                    ], [
                        'subject' => $timetable['subject'],
                    ]);
                }

            }
        }
    }
}
