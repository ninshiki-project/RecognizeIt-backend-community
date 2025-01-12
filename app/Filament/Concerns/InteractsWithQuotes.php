<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: InteractsWithQuotes.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Filament\Concerns;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

trait InteractsWithQuotes
{
    public function todayQuote()
    {
        return Cache::remember('quotes-'.auth()->user()->id, Carbon::now()->addHours(3), function () {
            return $this->quotes()->random();
        });
    }

    protected function quotes(): Collection
    {

        return new Collection([
            'Success is not the key to happiness. Happiness is the key to success.',
            'The only way to do great work is to love what you do.',
            'Don’t watch the clock; do what it does. Keep going.',
            'The future depends on what you do today.',
            'Believe in yourself and all that you are. Know that there is something inside you that is greater than any obstacle.',
            'The harder you work for something, the greater you’ll feel when you achieve it.',
            "Opportunities don't happen, you create them.",
            'Work hard in silence, let your success be your noise.',
            'Success is the sum of small efforts, repeated day in and day out.',
            'The only limit to our realization of tomorrow is our doubts of today.',
            'Strive not to be a success, but rather to be of value.',
            'Don’t be afraid to give up the good to go for the great.',
            'You are never too old to set another goal or to dream a new dream.',
            'It does not matter how slowly you go as long as you do not stop.',
            'Act as if what you do makes a difference. It does.',
            'Hardships often prepare ordinary people for an extraordinary destiny.',
            'Success usually comes to those who are too busy to be looking for it.',
            'The only place where success comes before work is in the dictionary.',
            'Your limitation—it’s only your imagination.',
            'Great things never come from comfort zones.',
            'Push yourself, because no one else is going to do it for you.',
            'Dream it. Wish it. Do it.',
            'Success is not in what you have, but who you are.',
            'You don’t have to be great to start, but you have to start to be great.',
            'The key to success is to focus on goals, not obstacles.',
            'Believe in your infinite potential. Your only limitations are those you set upon yourself.',
            'Every day is a new beginning. Take a deep breath and start again.',
            'The only way to achieve the impossible is to believe it is possible.',
            'Sometimes we’re tested not to show our weaknesses, but to discover our strengths.',
            "Don't stop when you're tired. Stop when you're done.",
            'Great things are not done by impulse, but by a series of small things brought together.',
            'You are capable of amazing things.',
            'Take the risk or lose the chance.',
            'Don’t wait for opportunity. Create it.',
            'Your passion is waiting for your courage to catch up.',
            'The way to get started is to quit talking and begin doing.',
            "Don't limit your challenges. Challenge your limits.",
            'If you want to achieve greatness stop asking for permission.',
            'Success is not final, failure is not fatal: It is the courage to continue that counts.',
            'Do something today that your future self will thank you for.',

            // Relationship Quotes
            "Love is not about how much you say 'I love you', but how much you prove that it's true.",
            'In a relationship, the best way to keep things fresh is to stay true to each other and grow together.',
            'A successful relationship requires falling in love many times, but always with the same person.',
            'The best thing to hold onto in life is each other.',
            'True love is not about perfection, it’s about accepting each other’s flaws and growing together.',
            'Good relationships don’t just happen; they take time, patience, and two people who truly want to be together.',
            'The greatest thing you’ll ever learn is just to love and be loved in return.',
            'A true relationship is two imperfect people refusing to give up on each other.',
            'Sometimes the heart sees what is invisible to the eye.',
            'Love is when the other person’s happiness is more important than your own.',
            'The best relationships are the ones you never saw coming.',
            'A strong relationship requires choosing to love each other, even on the days when you don’t like each other.',
            'The best love is the one that makes you a better person without changing you into someone other than yourself.',
            'It’s not about finding someone to live with; it’s about finding someone you can’t imagine living without.',
            'Don’t ever make someone a priority when all you are to them is an option.',
            'Love is not about how many days, months, or years you have been together, it’s about how much you love each other every single day.',
            'The right person will come into your life when you least expect it.',
            'In the end, we only regret the chances we didn’t take and the love we didn’t show.',
            'The best relationships are built on trust, communication, and mutual respect.',
            'Sometimes, the heart speaks louder than words.',
            'True love doesn’t come to you, it’s something you have to work for.',
            'A healthy relationship is built on equality, trust, and respect.',
            'Love isn’t something you find. Love is something that finds you.',
            'Respect is the foundation of every great relationship.',
            'If you want to go fast, go alone. If you want to go far, go together.',
            'Love isn’t perfect, but it’s worth it.',
            'When someone truly loves you, they will never let you go, no matter how tough the situation is.',
            'In a relationship, sometimes silence says more than words ever could.',
            'The best relationship is when you can be yourselves and never feel judged.',
            'A relationship is about falling in love with the same person over and over again, without getting bored.',

            // Jokes to make the user smile
            'Why don’t skeletons fight each other? They don’t have the guts.',
            'I told my wife she was drawing her eyebrows too high. She looked surprised.',
            'I used to play piano by ear, but now I use my hands.',
            "Why don't scientists trust atoms? Because they make up everything!",
            'I couldn’t figure out how to put my seatbelt on… then it clicked.',
            'Parallel lines have so much in common. It’s a shame they’ll never meet.',
            'I’m reading a book on anti-gravity. It’s impossible to put down!',
            'Why don’t oysters share their pearls? Because they’re shellfish.',
            'I told my computer I needed a break, and now it won’t stop sending me KitKats.',
            'What did the ocean say to the beach? Nothing, it just waved.',
            'What do you call fake spaghetti? An impasta!',
            'I used to be a baker, but I couldn’t make enough dough.',
            'How does a penguin build its house? Igloos it together.',
            'I’m on a whiskey diet. I’ve lost three days already!',
            'What do you call cheese that isn’t yours? Nacho cheese!',
            'I couldn’t figure out how to do a math problem… until I finally squared it away.',
            'Why do cows have hooves instead of feet? Because they lactose.',
            'I went to a seafood disco last week… and pulled a mussel.',
            'I told my wife she was drawing her eyebrows too high. She looked surprised.',
            'Why don’t skeletons fight each other? They don’t have the guts.',
            'How do you organize a space party? You planet.',
            'What do you get when you cross a snowman and a vampire? Frostbite.',
            'I’m friends with all electricians. We have good current connections.',
            'How does Moses make his coffee? He brews it.',
            "I used to be a baker, but I couldn't make enough dough.",
            'What did one plate say to the other? Lunch is on me.',
            'What’s orange and sounds like a parrot? A carrot!',
            'What did one ocean say to the other ocean? Nothing, they just waved.',
            'How do you make a tissue dance? Put a little boogey in it.',
            'I’m writing a book on reverse psychology. Don’t buy it.',
        ]);

    }
}
