<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Setting;

class DefaultHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (app()->environment() == 'production') {
            Setting::create([
                'name' => 'default_head',
                'namespace' => 'personal',
                'note' => '默认头像',
                'contents' => [
                    "https://dn-platform-1253386414.file.myqcloud.com/default_avatar/20181129/1.png",
                    "https://dn-platform-1253386414.file.myqcloud.com/default_avatar/20181129/2.png",
                    "https://dn-platform-1253386414.file.myqcloud.com/default_avatar/20181129/3.png",
                    "https://dn-platform-1253386414.file.myqcloud.com/default_avatar/20181129/4.png",
                    "https://dn-platform-1253386414.file.myqcloud.com/default_avatar/20181129/5.png",
                    "https://dn-platform-1253386414.file.myqcloud.com/default_avatar/20181129/6.png",
                    "https://dn-platform-1253386414.file.myqcloud.com/default_avatar/20181129/7.png",
                ],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        } else {
            Setting::create([
                'name' => 'default_head',
                'namespace' => 'personal',
                'note' => '默认头像',
                'contents' => [
                    "https://dn-platform-test-1253386414.file.myqcloud.com/2018/11/27/1747/W0GCaScKdl4G74upkkJA1LafyR9rpoWPuMeKmlBj.png",
                    "https://dn-platform-test-1253386414.file.myqcloud.com/2018/11/27/1749/IRHLjyS5U0qTp6qH0cNm0SivonD4rcD0j4E9gAXm.png",
                    "https://dn-platform-test-1253386414.file.myqcloud.com/2018/11/27/1749/4qZc4KCWYkfffCNfV5EYKV4RmMjaMhZt01vE6GG5.png",
                    "https://dn-platform-test-1253386414.file.myqcloud.com/2018/11/27/1750/T58Sj9ubqX9U4fjpo2qlIzyWOz4Phxp3THThRjv4.png",
                    "https://dn-platform-test-1253386414.file.myqcloud.com/2018/11/27/1750/LdBn5hzsw2410aR5UYk9RVu6WVjt0ECBfOBQ3hDQ.png",
                    "https://dn-platform-test-1253386414.file.myqcloud.com/2018/11/27/1750/WMGGakRI3SMV49FwzYXkC3hMZ5g5y8J4bgX1WTW4.png",
                    "https://dn-platform-test-1253386414.file.myqcloud.com/2018/11/27/1750/hbtczQqwKL7qNankPz1t059KvqSAbaS6opMqU9Yb.png"
                ],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

    }
}
