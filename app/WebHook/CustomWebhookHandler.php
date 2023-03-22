<?php

namespace App\WebHook;

use App\Helpers\KeyboardHelper;
use App\Models\Timetable;
use App\Models\User;
use Carbon\Carbon;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;

class CustomWebhookHandler extends WebhookHandler
{
    public function handleChatMessage(Stringable $text): void
    {
        Log::info($this->message);
    }

    protected function handleUnknownCommand(Stringable $text): void
    {
        $this->chat->html("Я такое не понимаю: $text")->send();
    }

    public function start()
    {
        $name = $this->message->from()->firstName() ?? 'No name';
        $tg_id = $this->message->from()->id();

        $user = User::query()->updateOrCreate(['tg_id' => $tg_id], ['name' => $name]);
        if ($user->group_id !== null) {
            $this->chat->markdown("Привет! Что хочешь?")->keyboard(KeyboardHelper::getMainKeyboard($tg_id))->send();
        } else {
            $this->chat->markdown("Привет **$name**, рад тебя видеть тут! Для начала мне нужно узнать побольше о тебе. В каком институте ты обучаешься?")
                ->keyboard(KeyboardHelper::getUniversityKeyboard($tg_id))
                ->send();
        }

    }

    public function addUniversity()
    {
        $institute_id = $this->data->get('id');
        $user_id = $this->data->get('user_id');
        $this->chat->markdown("Отлично! это мой любимый институт! В какой группе ты учишься?")
            ->keyboard(KeyboardHelper::getGroupsKeyboard($institute_id, $user_id))
            ->send();

    }

    public function addGroup()
    {
        $group_id = $this->data->get('id');
        $user_id = $this->data->get('user_id');

        User::query()->where('tg_id', $user_id)->update(['group_id' => $group_id]);

        $this->chat->markdown("Класс! Если указал что то неверно заходи в настройки, для того чтобы получить расписание, жми 'Расписание'")->keyboard(KeyboardHelper::getMainKeyboard($user_id))->send();
    }

    public function selectTimetable()
    {
        $tg_id = $this->data->get('user_id');
        $this->chat->markdown('Каую дату хочешь посмотреть?')->keyboard(KeyboardHelper::getDateTimetableKeyboards($tg_id))->send();
    }

    public function getTimetable()
    {
        $tg_id = $this->data->get('user_id');
        $date = $this->data->get('date');

        $user = User::query()->firstWhere('tg_id', $tg_id);
        $timetables = Timetable::query()->where('date', $date)->where('group_id', $user->group_id)->get();

        $message = "";
        foreach ($timetables as $timetable) {
            $message .= "<pre>$timetable->time - $timetable->subject</pre>";
        }
        $this->chat->html("<pre>Расписание занятий на $date</pre>" . $message)->send();
    }

    public function changeSettings()
    {
        $tg_id = $this->data->get('user_id');
        User::query()->firstWhere('tg_id', $tg_id)->delete();
        $this->chat->markdown('Чтобы начать все заново - жми /start')->send();
    }
}
