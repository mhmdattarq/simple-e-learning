<?php

namespace App\Routing;

use App\Models\Course;
use App\Models\Quiz;
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

            if (is_array($parameters)) {
                if (isset($parameters['course_id']) && ! isset($parameters['course'])) {
                    $parameters['course'] = $parameters['course_id'];
                    unset($parameters['course_id']);
                }

                if (isset($parameters['quiz_id']) && ! isset($parameters['quiz'])) {
                    $parameters['quiz'] = $parameters['quiz_id'];
                    unset($parameters['quiz_id']);
                }

                if (isset($parameters['quiz']) && is_numeric($parameters['quiz'])) {
                    $quizModel = Quiz::find((int) $parameters['quiz']);
                    if ($quizModel) {
                        $parameters['quiz'] = $quizModel;
                    }
                }
            }

            return parent::route("peserta.evaluasi.kerjakan.{$typeSuffix}", $parameters, $absolute);
        }

        if (str_starts_with($name, 'peserta.evaluasi.kerjakan.')) {
            if (is_array($parameters)) {
                if (isset($parameters['quiz_id']) && ! isset($parameters['quiz'])) {
                    $parameters['quiz'] = $parameters['quiz_id'];
                    unset($parameters['quiz_id']);
                }

                if (isset($parameters['quiz']) && is_numeric($parameters['quiz'])) {
                    $quizModel = Quiz::find((int) $parameters['quiz']);
                    if ($quizModel) {
                        $parameters['quiz'] = $quizModel;
                    }
                }
            }

            return parent::route($name, $parameters, $absolute);
        }

        if ($name === 'peserta.evaluasi.show') {
            if (is_array($parameters)) {
                if (isset($parameters['quiz_id']) && ! isset($parameters['quiz'])) {
                    $parameters['quiz'] = $parameters['quiz_id'];
                    unset($parameters['quiz_id']);
                }

                if (isset($parameters['quiz']) && is_numeric($parameters['quiz'])) {
                    $quizModel = Quiz::find((int) $parameters['quiz']);
                    if ($quizModel) {
                        $parameters['quiz'] = $quizModel;
                    }
                }
            }

            return parent::route($name, $parameters, $absolute);
        }

        return parent::route($name, $parameters, $absolute);
    }
}
