<?php

namespace Modules\SocialSync\app\Http\Controllers\V1;

use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\CoreController;
use Modules\SocialSync\app\Http\Requests\V1\GenerateImageRequest;
use Modules\SocialSync\app\Http\Requests\V1\GenerateTextRequest;
use Modules\SocialSync\app\Services\GenerateService;

class GenerateController extends CoreController
{
    public function __construct(
        private readonly GenerateService $generateService
    ) {}

    /**
     * Generate text content based on subject.
     */
    public function generateText(GenerateTextRequest $request)
    {
        try {
            $subject = $request->input('subject');
            $text = $this->generateService->textGenerate($subject);

            if (!$text) {
                return failedResponse(__('socialsync::messages.generate.text.error'), 500);
            }

            return successResponse(['content' => $text], __('socialsync::messages.generate.text.success'));
        } catch (\Exception $e) {
            return failedResponse(__('socialsync::messages.generate.text.failed'), 500);
        }
    }

    /**
     * Generate image based on subject.
     */
    public function generateImage(GenerateImageRequest $request)
    {
        try {
            $subject = $request->input('subject');
            $userId = auth('user')->id();

            $fileName = $this->generateService->imageGenerate($subject, $userId);

            if (!$fileName) {
                return failedResponse(__('socialsync::messages.generate.image.error'), 500);
            }

            return successResponse(['file_name' => $fileName], __('socialsync::messages.generate.image.success'));
        } catch (\Exception $e) {
            return failedResponse(__('socialsync::messages.generate.image.error'), 500);
        }
    }

    /**
     * Get the generated image for the authenticated user.
     */
    public function getGeneratedImage(Request $request)
    {
        $userId = auth('user')->id();
        $imageData = $this->generateService->getGeneratedImage($userId);
        if(!$imageData) {
            return successResponse(null);
        }
        return fileResponse($imageData);
    }
}

