<?php
namespace App\Helpers;
use App\Models\Group;
use App\Models\Institute;
use Carbon\Carbon;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Keyboard\Button;
class KeyboardHelper
{
    public static function getUniversityKeyboard($tg_id):Keyboard
    {
        $institutes = Institute::query()->get();
        $buttons = [];
        foreach ($institutes as $institute){
            $buttons[] = Button::make($institute->title)->action('addUniversity')->param('id', $institute->id)->param('user_id', $tg_id);
        }
        return Keyboard::make()->buttons($buttons);
    }
    public static function getGroupsKeyboard($institute_id, $user_id):Keyboard
    {
        $groups = Group::query()->where('institute_id', $institute_id)->get();
        $keyboard = Keyboard::make();
        $row_keyboard = [];
        foreach ($groups as $key => $group) {
            $row_keyboard[] = Button::make($group->title)->action('addGroup')->param('id', $group->id)->param('user_id', $user_id);
            if($key !== 0 AND $key % 3 == 0){
                $keyboard->row($row_keyboard);
                $row_keyboard = [];
            }
        }
        return $keyboard;
    }
    public static function getMainKeyboard($user_id){
        return Keyboard::make()->buttons([
           Button::make('Расписание')->action('selectTimetable')->param('user_id', $user_id),
           Button::make('Сменить свои данные')->action('changeSettings')->param('user_id', $user_id),
        ]);
    }
    public static function getDateTimetableKeyboards($tg_id){
        $keyboard = Keyboard::make();
        $buttons = [];
        $start_date = Carbon::now();
        for ($i = 0; $i < 5; $i++){
            $buttons[] = Button::make($start_date->format('d-m-Y'))->action('getTimetable')->param('date', $start_date->format('Y-m-d'))->param('user_id', $tg_id);
            $start_date->addDay();
        }
        return $keyboard->buttons($buttons);
    }
}
