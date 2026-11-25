<?php

class MailBody extends Model {

    protected static string $tableName = 'distribute_mail_body';


    public static function add($name, $subject, $body, $notes) {
        self::insert([
            'name' => $name,
            'subject' => $subject,
            'body' => $body,
            'notes' => $notes,
        ]);
    }

    public static function modify($id, $name, $subject, $body, $notes) {
        self::where('id', $id)->update([
            'name' => $name,
            'subject' => $subject,
            'body' => $body,
            'notes' => $notes,
        ]);
    }

    public static function disable($id) {
        self::where('id', $id)->update(['disable' => 1]);
    }

}