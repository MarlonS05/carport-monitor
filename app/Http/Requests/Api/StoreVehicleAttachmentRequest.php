<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Mobile;
use App\Models\Vehicle;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Validator;

final class StoreVehicleAttachmentRequest extends MobileApiRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...$this->mobileIdRules(),
            'id' => ['required', 'uuid', 'unique:vehicle_attachments,external_id'],
            'file' => ['required', 'file', 'max:10240', 'mimes:jpeg,jpg,png,gif,webp,pdf'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'mobile_id.required' => 'The X-Mobile-Id header is required.',
            'mobile_id.uuid' => 'The X-Mobile-Id header must be a valid UUID.',
            'mobile_id.exists' => 'The X-Mobile-Id header does not match a registered mobile device.',
            'id.required' => 'The attachment id is required.',
            'id.uuid' => 'The attachment id must be a valid UUID.',
            'id.unique' => 'An attachment with this id already exists.',
            'file.required' => 'The attachment file is required.',
            'file.file' => 'The attachment must be a valid uploaded file.',
            'file.max' => 'The attachment file must not exceed 10 MB.',
            'file.mimes' => 'The attachment file must be a JPEG, PNG, GIF, WebP image, or PDF.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'mobile_id' => 'X-Mobile-Id header',
            'id' => 'attachment id',
            'file' => 'attachment file',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $vehicle = $this->route('vehicle');

            if (! $vehicle instanceof Vehicle) {
                return;
            }

            if ($vehicle->mobile_id !== $this->input('mobile_id')) {
                throw new AuthorizationException('This vehicle is not owned by the requesting mobile device.');
            }
        });
    }

    protected function failedAuthorization(): void
    {
        throw new AuthorizationException('This vehicle is not owned by the requesting mobile device.');
    }

    public function mobile(): Mobile
    {
        return Mobile::query()->findOrFail($this->validated('mobile_id'));
    }

    public function attachmentId(): string
    {
        return $this->validated('id');
    }

    public function uploadedFile(): UploadedFile
    {
        /** @var UploadedFile $file */
        $file = $this->validated('file');

        return $file;
    }
}
