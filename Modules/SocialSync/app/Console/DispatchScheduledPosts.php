<?php

namespace Modules\SocialSync\app\Console;

use Illuminate\Console\Command;
use Modules\SocialSync\app\Enums\PostStatus;
use Modules\SocialSync\app\Jobs\PublishPostJob;
use Modules\SocialSync\app\Services\PostService;
use Symfony\Component\Console\Command\Command as CommandAlias;

class DispatchScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:dispatch-scheduled-posts';

    /**
     * The console command description.
     */
    protected $description = 'Dispatches posts from the database that are due for publishing.';

    /**
     * Create a new command instance.
     */
    public function __construct(
        private readonly PostService $postService,
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle() {
        $posts = $this->postService->getScheduledPosts()->get();

        foreach ($posts as $post) {
            $this->postService->update($post->id, ['status' => PostStatus::PENDING]);

            foreach ($post->socialAccounts as $socialAccount) {
                PublishPostJob::dispatch($post->id, $socialAccount->id);
            }

            $this->postService->setPublishStatus($post->id);


            $this->info("Dispatched Post #{$post->id} for publishing.");
        }

        return CommandAlias::SUCCESS;
    }

}
