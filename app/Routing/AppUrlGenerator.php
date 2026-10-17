<?php

namespace App\Routing;

use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Routing\UrlGenerator;

class AppUrlGenerator extends UrlGenerator
{
    /**
     * Resolve type suffix for course (bulanan, bangkom, or bangkom_external).
     */
    protected function resolveCourseTypeSuffix(mixed $course): string
    {
        $type = null;

        if ($course instanceof Course) {
            $type = $course->type;
        } elseif (is_numeric($course)) {
            $type = Course::where('id', (int) $course)->value('type');
        } elseif (is_string($course) && ! empty($course)) {
            $type = Course::where('slug', $course)->value('type');
        }

        return match ($type) {
            'bangkom', 'permanent' => 'bangkom',
            'bangkom_external', 'paid', 'berbayar' => 'bangkom_external',
            default => 'bulanan',
        };
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
        // 1. Backward-compatible mapping for catalog list route names
        $aliasMap = [
            'landing.kelas.batch' => 'landing.kelas.bulanan',
            'landing.kelas.permanen' => 'landing.kelas.bangkom',
            'landing.kelas.berbayar' => 'landing.kelas.bangkom_external',
            'landing.kelas.detail.batch' => 'landing.kelas.detail.bulanan',
            'landing.kelas.detail.permanen' => 'landing.kelas.detail.bangkom',
            'landing.kelas.detail.berbayar' => 'landing.kelas.detail.bangkom_external',
            'peserta.materi.batch' => 'peserta.materi.bulanan',
            'peserta.materi.permanen' => 'peserta.materi.bangkom',
            'peserta.materi.berbayar' => 'peserta.materi.bangkom_external',
            'peserta.evaluasi.kerjakan.batch' => 'peserta.evaluasi.kerjakan.bulanan',
            'peserta.evaluasi.kerjakan.permanen' => 'peserta.evaluasi.kerjakan.bangkom',
            'peserta.evaluasi.kerjakan.berbayar' => 'peserta.evaluasi.kerjakan.bangkom_external',
        ];

        if (isset($aliasMap[$name])) {
            $name = $aliasMap[$name];
        }

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
