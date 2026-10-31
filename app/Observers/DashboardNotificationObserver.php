<?php

namespace App\Observers;

use App\Enums\DashboardNotificationTypeEnum;
use App\Enums\OrderStatusEnum;
use App\Models\DashboardNotification;
use App\Models\Kyc;
use App\Models\Order;
use App\Models\PaymentRequest;
use App\Models\Project;
use App\Models\RefundRequest;
use App\Models\Report;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Throwable;

/**
 * Creates the admin dashboard notifications (in Arabic) for the events the
 * admin has to act on: new projects/services to review, orders, cancel
 * requests, disputes, refunds, withdrawals, reports and KYC requests.
 */
class DashboardNotificationObserver
{
    /**
     * A record was created
     *
     * @param Model $model
     *
     * @return void
     */
    public function created(Model $model): void
    {
        match (true) {
            $model instanceof Project => $this->notify(
                'مشروع جديد بانتظار المراجعة',
                'أضاف ' . $this->userName($model->user) . ' مشروع «' . $model->title . '» ويحتاج إلى اعتماد.'
            ),
            $model instanceof Service => $model->custom_offer ? null : $this->notify(
                'خدمة جديدة بانتظار المراجعة',
                'أضاف ' . $this->userName($model->user) . ' خدمة «' . $model->title . '» وتحتاج إلى اعتماد.'
            ),
            $model instanceof Order => $this->notify(
                'طلب جديد',
                'طلب جديد من ' . $this->userName($model->seeker) . ' إلى ' . $this->userName($model->provider)
                . ' بقيمة ' . $this->money($model->price) . '.'
            ),
            $model instanceof RefundRequest => $this->notify(
                'طلب استرداد جديد',
                'تم إنشاء طلب استرداد بقيمة ' . $this->money($model->amount) . ' ويحتاج إلى مراجعة.'
            ),
            $model instanceof PaymentRequest => $this->notify(
                'طلب سحب جديد',
                'طلب ' . $this->userName($model->user) . ' سحب ' . $this->money($model->amount) . '.'
            ),
            $model instanceof Report => $this->notify(
                'بلاغ جديد',
                'أرسل ' . $this->userName($model->user) . ' بلاغًا: «' . $model->title . '».',
                DashboardNotificationTypeEnum::DISPUTE
            ),
            $model instanceof Kyc => $this->notify(
                'طلب توثيق هوية جديد',
                'أرسل ' . $this->userName($model->user) . ' طلب توثيق الهوية ويحتاج إلى مراجعة.'
            ),
            default => null,
        };
    }

    /**
     * A record was updated: only order cancel requests and disputes matter here
     *
     * @param Model $model
     *
     * @return void
     */
    public function updated(Model $model): void
    {
        if (!$model instanceof Order || !$model->wasChanged('status')) {
            return;
        }

        $details = 'الطلب بين ' . $this->userName($model->seeker) . ' و ' . $this->userName($model->provider)
            . ' بقيمة ' . $this->money($model->price) . '.';

        if ($model->status === OrderStatusEnum::CANCEL_PENDING->value) {
            $this->notify('طلب إلغاء طلب', 'طُلب إلغاء ' . $details . ' بانتظار قرار الإدارة.', DashboardNotificationTypeEnum::DISPUTE);
        } elseif ($model->status === OrderStatusEnum::DISPUTED->value) {
            $this->notify('نزاع جديد على طلب', 'تم فتح نزاع على ' . $details, DashboardNotificationTypeEnum::DISPUTE);
        }
    }

    /**
     * Store the notification; never let it break the action that triggered it
     *
     * @param string $title
     * @param string $details
     * @param DashboardNotificationTypeEnum $type
     *
     * @return void
     */
    private function notify(string $title, string $details, DashboardNotificationTypeEnum $type = DashboardNotificationTypeEnum::NORMAL): void
    {
        try {
            DashboardNotification::create([
                'title' => $title,
                'details' => $details,
                'type' => $type->value,
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    /**
     * @param mixed $user
     *
     * @return string
     */
    private function userName(mixed $user): string
    {
        return $user?->name ?? 'مستخدم';
    }

    /**
     * @param mixed $amount
     *
     * @return string
     */
    private function money(mixed $amount): string
    {
        return number_format((float) $amount, 2) . ' ريال';
    }
}
