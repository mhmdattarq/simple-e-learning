<?php

namespace App\Routing;

use App\Models\Course;
use Illuminate\Routing\UrlGenerator;

class AppUrlGenerator extends UrlGenerator
{
    /**
     * Resolve type suffix for course (batch, permanen, or berbayar).
     */
    protected function resolveCourseTypeSuffix(mixed $course): string
    {
        if ($course instanceof Course) {
            return match ($course->type) {
                'permanent' => 'permanen',
                'paid' => 'berbayar',
                default => 'batch',
            };
        }

        if (is_numeric($course)) {
            $type = Course::where('id', (int) $course)->value('type');

            return match ($type) {
                'permanent' => 'permanen',
                'paid' => 'berbayar',
                default => 'batch',
            };
        }

        if (is_string($course) && ! empty($course)) {
            $type = Course::where('slug', $course)->value('type');

            return match ($type) {
                'permanent' => 'permanen',
                'paid' => 'berbayar',
                default => 'batch',
            };
        }

        return 'batch';
    }

    /**
     * Generate the URL to a named route.
     *
     * @param  string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     */
    public function route($name, $parameters = [], $absolute = true)
    {
        if ($name === 'landing.kelas.detail') {
            $course = is_array($parameters) ? ($parameters['course'] ?? reset($parameters)) : $parameters;
            $typeSuffix = $this->resolveCourseTypeSuffix($course);

            return parent::route("landing.kelas.detail.{$typeSuffix}", $parameters, $absolute);
        }

        if ($name === 'peserta.materi') {
            $course = is_array($parameters) ? ($parameters['course'] ?? reset($parameters)) : $parameters;
            $typeSuffix = $this->resolveCourseTypeSuffix($course);

            return parent::route("peserta.materi.{$typeSuffix}", $parameters, $absolute);
        }

        if ($name === 'peserta.evaluasi.kerjakan') {
            $course = is_array($parameters) ? ($parameters['course'] ?? ($parameters['course_id'] ?? reset($parameters))) : $parameters;
            $typeSuffix = $this->resolveCourseTypeSuffix($course);

            if (is_array($parameters) && isset($parameters['course_id']) && ! isset($parameters['course'])) {
                $parameters['course'] = $parameters['course_id'];
                unset($parameters['course_id']);
            }

            return parent::route("peserta.evaluasi.kerjakan.{$typeSuffix}", $parameters, $absolute);
        }

        return parent::route($name, $parameters, $absolute);
    }
}
