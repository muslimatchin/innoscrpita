<?php

namespace Database\Factories;

use App\Models\Source;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'source_id'      => Source::factory(),  // Assuming Source is a model with a factory
            'author_id'      => User::factory(),    // Assuming author is a User model with a factory
            'title'          => $this->faker->sentence,
            'description'    => $this->faker->text(150),
            'content'        => $this->faker->paragraphs(3, true), // Generates 3 paragraphs
            'url'            => $this->faker->url,
            'url_to_image'   => $this->faker->imageUrl(),
            'published_at'   => $this->faker->dateTimeThisYear(),
            'provider'       => $this->faker->company,
            'type'           => $this->faker->word, // Assuming 'type' is a single word string
            'additional_data'=> null, // You can store any extra info as a string

        ];
    }
}
