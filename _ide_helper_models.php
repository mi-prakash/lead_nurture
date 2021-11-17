<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Appointment
 *
 * @property int $id
 * @property int $appointment_id
 * @property int|null $user_id
 * @property int $lead_id
 * @property string $date
 * @property string $date_time
 * @property string $time
 * @property string $end_time
 * @property string $converted_time
 * @property string $converted_end_time
 * @property string $date_created
 * @property string $date_time_created
 * @property float|null $price
 * @property float|null $price_sold
 * @property string|null $paid
 * @property float|null $amount_paid
 * @property string|null $type
 * @property int|null $appointment_type_id
 * @property int|null $calendar_id
 * @property string|null $timezone
 * @property string|null $calendar_timezone
 * @property int|null $canceled
 * @property int|null $can_client_cancel
 * @property int|null $can_client_reschedule
 * @property string $status
 * @property string|null $json_data
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AppointmentLog[] $appointmentLogs
 * @property-read int|null $appointment_logs_count
 * @property-read \App\Models\Lead $lead
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereAmountPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereAppointmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereAppointmentTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereCalendarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereCalendarTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereCanClientCancel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereCanClientReschedule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereCanceled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereConvertedEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereConvertedTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereDateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereDateTimeCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereJsonData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment wherePriceSold($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment whereUserId($value)
 */
	class Appointment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AppointmentLog
 *
 * @property int $id
 * @property int $appointment_id
 * @property string $json_data
 * @property string $status
 * @property int $is_adjusted
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\Appointment $appointment
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentLog whereAppointmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentLog whereIsAdjusted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentLog whereJsonData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AppointmentLog whereUpdatedAt($value)
 */
	class AppointmentLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Campaign
 *
 * @property int $id
 * @property int $campaign_tree_id
 * @property string $name
 * @property string $description
 * @property int $is_reminder
 * @property int|null $before_hours
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\CampaignTree $campaign_tree
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\LeadCampaign[] $lead_campaigns
 * @property-read int|null $lead_campaigns_count
 * @property-read \App\Models\UserCampaign $user_campaign
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign query()
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereBeforeHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereCampaignTreeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereIsReminder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereUpdatedAt($value)
 */
	class Campaign extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CampaignCategory
 *
 * @property int $id
 * @property string $name
 * @property string $status
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CampaignTree[] $campaign_trees
 * @property-read int|null $campaign_trees_count
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignCategory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignCategory whereUpdatedAt($value)
 */
	class CampaignCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CampaignMessage
 *
 * @property int $id
 * @property int $campaign_id
 * @property string $wait
 * @property int|null $days
 * @property string|null $delivery_time
 * @property int|null $time
 * @property string|null $name
 * @property string|null $body
 * @property string|null $media_url
 * @property int $ordering
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\Campaign $campaign
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Rule[] $rules
 * @property-read int|null $rules_count
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignMessage whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignMessage whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignMessage whereDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignMessage whereDeliveryTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignMessage whereMediaUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignMessage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignMessage whereOrdering($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignMessage whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignMessage whereWait($value)
 */
	class CampaignMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CampaignTree
 *
 * @property int $id
 * @property int $campaign_category_id
 * @property string $name
 * @property string $status
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\CampaignCategory $campaign_category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Campaign[] $campaigns
 * @property-read int|null $campaigns_count
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignTree newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignTree newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignTree query()
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignTree whereCampaignCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignTree whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignTree whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignTree whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignTree whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignTree whereUpdatedAt($value)
 */
	class CampaignTree extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CampaignTrigger
 *
 * @property int $id
 * @property int $campaign_category_id
 * @property int $campaign_tree_id
 * @property string $type
 * @property int $campaign_id
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignTrigger newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignTrigger newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignTrigger query()
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignTrigger whereCampaignCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignTrigger whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignTrigger whereCampaignTreeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignTrigger whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignTrigger whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignTrigger whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignTrigger whereUpdatedAt($value)
 */
	class CampaignTrigger extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CustomField
 *
 * @property int $id
 * @property string $type
 * @property int|null $campaign_category_id
 * @property int|null $campaign_tree_id
 * @property string|null $name
 * @property string|null $value
 * @property string $data_type
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\UserCustomField $user_custome_field
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField whereCampaignCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField whereCampaignTreeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField whereDataType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField whereValue($value)
 */
	class CustomField extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Lead
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string $email
 * @property int $is_appointment
 * @property string|null $created_by
 * @property string|null $json_data
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Appointment[] $appointments
 * @property-read int|null $appointments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\LeadCampaign[] $lead_campaigns
 * @property-read int|null $lead_campaigns_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Lead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lead query()
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereIsAppointment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereJsonData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereUserId($value)
 */
	class Lead extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LeadCampaign
 *
 * @property int $id
 * @property int $lead_id
 * @property int $campaign_id
 * @property string $status
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\Campaign $campaign
 * @property-read \App\Models\Lead $lead
 * @method static \Illuminate\Database\Eloquent\Builder|LeadCampaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadCampaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadCampaign query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadCampaign whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadCampaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadCampaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadCampaign whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadCampaign whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadCampaign whereUpdatedAt($value)
 */
	class LeadCampaign extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Message
 *
 * @property int $id
 * @property int $lead_id
 * @property int|null $user_id
 * @property int|null $campaign_message_id
 * @property string $message
 * @property int $is_incoming
 * @property int $status
 * @property string|null $json_data
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\Lead $lead
 * @method static \Illuminate\Database\Eloquent\Builder|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCampaignMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereIsIncoming($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereJsonData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUserId($value)
 */
	class Message extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MessageQueue
 *
 * @property int $id
 * @property int $lead_id
 * @property int $user_id
 * @property int $campaign_message_id
 * @property string $message
 * @property string|null $send_timer
 * @property int $is_reminder
 * @property string|null $wait
 * @property string $status
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\CampaignMessage $campaign_message
 * @property-read \App\Models\Lead $lead
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|MessageQueue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageQueue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageQueue query()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageQueue whereCampaignMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageQueue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageQueue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageQueue whereIsReminder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageQueue whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageQueue whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageQueue whereSendTimer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageQueue whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageQueue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageQueue whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageQueue whereWait($value)
 */
	class MessageQueue extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MessageRuleCategory
 *
 * @property int $id
 * @property string $name
 * @property int $added_by
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RuleExpression[] $expressions
 * @property-read int|null $expressions_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|MessageRuleCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageRuleCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageRuleCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageRuleCategory whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageRuleCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageRuleCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageRuleCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageRuleCategory whereUpdatedAt($value)
 */
	class MessageRuleCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Rule
 *
 * @property int $id
 * @property int $message_id
 * @property string $execute_when
 * @property int|null $removed
 * @property int|null $add_to_campaign
 * @property string|null $expression_value
 * @property int|null $category
 * @property string|null $instant_reply
 * @property int|null $ordering
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\CampaignMessage $campaign_message
 * @method static \Illuminate\Database\Eloquent\Builder|Rule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rule query()
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereAddToCampaign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereExecuteWhen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereExpressionValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereInstantReply($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereOrdering($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereRemoved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rule whereUpdatedAt($value)
 */
	class Rule extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\RuleExpression
 *
 * @property int $id
 * @property int $message_rule_category_id
 * @property string $name
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\MessageRuleCategory $rule_category
 * @method static \Illuminate\Database\Eloquent\Builder|RuleExpression newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RuleExpression newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RuleExpression query()
 * @method static \Illuminate\Database\Eloquent\Builder|RuleExpression whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuleExpression whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuleExpression whereMessageRuleCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuleExpression whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RuleExpression whereUpdatedAt($value)
 */
	class RuleExpression extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Setting
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $click_funnel_email
 * @property string|null $click_funnel_api_key
 * @property string|null $click_funnel_webhook_url
 * @property string|null $click_funnel_name
 * @property string|null $click_funnel_id
 * @property string|null $acuity_user_id
 * @property string|null $acuity_api_key
 * @property string|null $acuity_webhook_url
 * @property string|null $acuity_calendar_name
 * @property string|null $acuity_calendar_id
 * @property string|null $twilio_account_sid
 * @property string|null $twilio_auth_token
 * @property string|null $twilio_number
 * @property string|null $twilio_webhook_url
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereAcuityApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereAcuityCalendarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereAcuityCalendarName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereAcuityUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereAcuityWebhookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereClickFunnelApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereClickFunnelEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereClickFunnelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereClickFunnelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereClickFunnelWebhookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereTwilioAccountSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereTwilioAuthToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereTwilioNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereTwilioWebhookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereUserId($value)
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TimezoneSetting
 *
 * @property int $id
 * @property string $type
 * @property int $user_id
 * @property string $timezone
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TimezoneSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimezoneSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimezoneSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|TimezoneSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimezoneSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimezoneSetting whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimezoneSetting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimezoneSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimezoneSetting whereUserId($value)
 */
	class TimezoneSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string|null $identifier
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property int|null $is_admin
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MessageQueue[] $message_queues
 * @property-read int|null $message_queues_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserCampaign[] $user_campaigns
 * @property-read int|null $user_campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserCustomField[] $user_custome_fields
 * @property-read int|null $user_custome_fields_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserCampaign
 *
 * @property int $id
 * @property int $user_id
 * @property int $campaign_category_id
 * @property int $campaign_tree_id
 * @property string $status
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\CampaignCategory $campaign_category
 * @property-read \App\Models\CampaignTree $campaign_tree
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Campaign[] $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserCampaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCampaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCampaign query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCampaign whereCampaignCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCampaign whereCampaignTreeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCampaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCampaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCampaign whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCampaign whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCampaign whereUserId($value)
 */
	class UserCampaign extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserCustomField
 *
 * @property int $id
 * @property int $user_id
 * @property int $custom_field_id
 * @property string $value
 * @property string $data_type
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\CustomField $custome_field
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserCustomField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCustomField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCustomField query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCustomField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCustomField whereCustomFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCustomField whereDataType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCustomField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCustomField whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCustomField whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCustomField whereValue($value)
 */
	class UserCustomField extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserTimeSetting
 *
 * @property int $id
 * @property int $user_id
 * @property string $from_time
 * @property string $to_time
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserTimeSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTimeSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTimeSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTimeSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTimeSetting whereFromTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTimeSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTimeSetting whereToTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTimeSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTimeSetting whereUserId($value)
 */
	class UserTimeSetting extends \Eloquent {}
}

