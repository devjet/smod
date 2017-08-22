<?php
namespace App\Repositories;

use ORM;
use Faker\Factory as Faker;

class FillerRepository
{


    public static function fillMessageService($name)
    {

        $mService = ORM::for_table('message_service')->where('name', $name)->findOne();

        if ($mService)
            return $mService->id;

        $mService = ORM::for_table('message_service')->create();
        $mService->name = $name;
        $mService->save();
        return $mService->id;

    }


    public static function fillMember($phone = null)
    {
        $phone = $phone ?: Faker::create()->phoneNumber;

        error_log($phone);
        $member = ORM::for_table('member')->where('phone', $phone)->findOne();

        if ($member)
            return $member->id;

        $member = ORM::for_table('member')->create();
        $member->phone = $phone;
        $member->save();
        return $member->id;

    }

    public static function fillMessageGroup($name = null)
    {
        $name = $name ?: Faker::create()->lastName;

        $mGroup = ORM::for_table('message_group')->where('name', $name)->find_one();

        if ($mGroup)
            return $mGroup->id;

        $mGroup = ORM::for_table('message_group')->create();
        $mGroup->name = $name;
        $mGroup->save();
        return $mGroup->id;
    }

    public static function fillMessageGroupMember($groupId, $memberId)
    {
        $mgMember = ORM::for_table('message_group_member')->create();
        $mgMember->group_id = $groupId;
        $mgMember->member_id = $memberId;
        $mgMember->save();

        return $mgMember->id;

    }

    public static function fillAttachment($type = null, $path = null, $fileName = null, $size = null)
    {

        $faker = Faker::create();

        $type = $type ?: $faker->mimeType;
        $path = $path ?: 'homepath/path/path/';//$faker->file('/tmp', '/tmp');
        $fileName = $fileName ?: 'file.jpg'; // $faker->file('/tmp', '/tmp', false);
        $size = $size ?: rand(1000, 300000);

        $att = ORM::for_table('attachment')->create();
        $att->type = $type;
        $att->path = $path;
        $att->filename = $fileName;
        $att->size = $size;
        $att->save();

        return $att->id;
    }

    public static function fillMessageAttachment($attachmentId, $messageId)
    {
        $ma = ORM::for_table('message_attachment')->create();
        $ma->attachment_id = $attachmentId;
        $ma->message_id = $messageId;
        $ma->save();
        return $ma->id;

    }

    public static function fillDevice($handle = null)
    {
        $handle = $handle ?: Faker::create()->uuid;

        $device = ORM::for_table('device')->where('handle', $handle)->find_one();
        if ($device)
            return $device->id;

        $device = ORM::for_table('device')->create();
        $device->handle = $handle;
        $device->save();
        return $device->id;
    }

    public static function fillMemberDevice($memberId, $deviceId)
    {
        $mDevice = ORM::for_table('member_device')->create();
        $mDevice->member_id = $memberId;
        $mDevice->device_id = $deviceId;
        $mDevice->save();

        return $mDevice->id;

    }

    public static function getConversationObjectById($conversationId)
    {
        return ORM::for_table('conversation')->find_one($conversationId);

    }

    public static function fillConversation($messageGroupId)
    {
        $conversation = ORM::for_table('conversation')->create();
        $conversation->last_message_id = null;
        $conversation->message_group_id = $messageGroupId;
        $conversation->save();
        return $conversation->id;

    }

    public static function fillAngGetMessageObject($deviceId, $groupId, $serviceId, $conversationId, $senderId, $receiverId = null, $text = null, $isAttachment = null)
    {

        $text = $text ?: (rand(1, 7) == 3 ? "SEND NUDES!!1" : Faker::create()->text());
        $isAttachment = $isAttachment ?: (rand(1, 3) == 2 ? 1 : 0);

        $msg = ORM::for_table('message')->create();
        $msg->device_id = $deviceId;
        $msg->group_id = $groupId;
        $msg->service_id = $serviceId;
        $msg->conversation_id = $conversationId;
        $msg->sender_id = $senderId;
        $msg->receiver_id = $receiverId;
        $msg->is_attachment = $isAttachment;
        $msg->text = $text;

        $msg->save();

        return $msg;
    }


    public static function truncate()
    {
        ORM::raw_execute('TRUNCATE message_service, member, device, member_device, conversation, message, attachment, message_attachment, message_group, message_group_member CASCADE');
    }



    /*
    *********** Flow example
     1. message service ->id
     2. device ->id
     3. member->id
     4. =>member_device  ->id

     5. message
        ?5.0.0 =>message_grop ->id
        ?5.0 =>message_group_member-> id
     5.1 conversation -> id
     5.2 attachment-> id
     5.3 =>message_attachment
    *************************
    */
    public static function fillEm()
    {



        $conversationId = null;
        $messageServiceId = self::fillMessageService('SMS');
        $deviceId = self::fillDevice();
        $memberId = self::fillMember();
        $memberDeviceId = self::fillMemberDevice($memberId, $deviceId);

        $messageGroupId = self::fillMessageGroup('first');
        $messageGroupMemberId = self::fillMessageGroupMember($messageGroupId, $memberId);

        $conversationObject = self::getConversationObjectById($conversationId);
        if (!$conversationObject) {
            $conversationId = self::fillConversation($messageGroupId);
            $conversationObject = self::getConversationObjectById($conversationId);
        }


        $attachmentId = self::fillAttachment();
        $messageObject = self::fillAngGetMessageObject(
            $deviceId,
            $messageGroupId,
            $messageServiceId,
            $conversationObject->id,
            $memberId,
            null,
            null,
            null); //if there's $attachmentId, set to 1

        self::fillMessageAttachment($attachmentId, $messageObject->id);
        $conversationObject->last_message_id = $messageObject->id;
        $conversationObject->save();
    }

}