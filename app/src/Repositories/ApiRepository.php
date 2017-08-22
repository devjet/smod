<?php

namespace App\Repositories;

use ORM;

class ApiRepository
{


    public static function getConversationList($offset = 0, $limit = null)
    {
        //TODO:
        //pagination
        //filter
        //time
        //..need 3-4h more

        $cObject = ORM::for_table('conversation')->offset($offset)->limit($limit);
        return $cObject->find_many();


    }

    public static function getMessageAttachments($messageId){
        $mAttObject = ORM::for_table('message_attachment')->where('message_attachment.message_id',$messageId);
        $mAttObject = $mAttObject->left_outer_join('attachment','message_attachment.attachment_id = attachment.id');
        return $mAttObject->find_many();
    }

    public static function getConversationMessages($conversationId, $offset = 0, $limit = null)
    {
        $cMessagesObject = ORM::for_table('message')->where('conversation_id', $conversationId);
        $cMessagesObject = $cMessagesObject->select_many(array('service_name'=>'message_service.name','message_id'=>'message.id'), '*');
        $cMessagesObject = $cMessagesObject->left_outer_join('message_service','message.service_id=message_service.id');
        $cMessagesObject = $cMessagesObject->offset($offset)->limit($limit);
        return $cMessagesObject->find_many();
    }

    public static function getConversationMembers($conversationId)
    {
        $query = <<<QUERY
        SELECT * FROM (SELECT
        "message_group_member"."member_id",
        "message_group_member"."group_id"
        FROM "conversation"
          LEFT OUTER JOIN "message_group_member" ON conversation.message_group_id = message_group_member.group_id
          WHERE "conversation"."id" = :conversation_id
        GROUP BY "message_group_member"."member_id", "message_group_member"."group_id") as nested_members
        LEFT JOIN member on nested_members.member_id = member.id;
QUERY;
        return ORM::for_table('conversation')
            ->raw_query($query, ['conversation_id' => $conversationId])->find_many();
    }
}
