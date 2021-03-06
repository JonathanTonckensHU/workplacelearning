<?php


namespace App\Analysis\Acting;


use App\Competence;
use App\LearningActivityActing;
use App\LearningGoal;
use App\ResourceMaterial;
use App\ResourcePerson;
use App\Timeslot;
use stdClass;

/**
 * Class Statistics provides easy access to statistics of user's activities
 * @package App\Analysis\Acting
 *
 * TODO: could be improved by caching statistics that are resource intensive (loops etc.)
 */
class Statistics
{
    private $analysisCollector;

    public function __construct(ActingAnalysisCollector $analysisCollector)
    {
        $this->analysisCollector = $analysisCollector;
    }


    /**
     * Get the percentage of activities in this timeslot
     *
     * @param Timeslot $timeslot
     * @return float
     */
    public function percentageActivitiesInTimeslot(Timeslot $timeslot)
    {
        $activities = $this->analysisCollector->getLearningActivities()->filter(
            function (LearningActivityActing $activity) use ($timeslot) {
                return $activity->timeslot->timeslot_id === $timeslot->timeslot_id;
            });

        if($this->analysisCollector->getLearningActivities()->count() > 0) {
            return round(($activities->count() / $this->analysisCollector->getLearningActivities()->count())*100, 1);
        } else {
            return 0;
        }
    }

    /**
     * Get the percentage of activities with this learning goal
     *
     * @param LearningGoal $learningGoal
     * @return float
     */
    public function percentageActivityForLearningGoal(LearningGoal $learningGoal)
    {
        $activities = $this->analysisCollector->getLearningActivities()->filter(
            function (LearningActivityActing $activity) use ($learningGoal) {
                return $activity->learningGoal->learninggoal_id === $learningGoal->learninggoal_id;
            });

        if($this->analysisCollector->getLearningActivities()->count() > 0) {
            return round(($activities->count() / $this->analysisCollector->getLearningActivities()->count()) * 100, 1);
        } else {
            return 0;
        }
    }

    /**
     * Get the percentage of activities with this competence
     *
     * @param Competence $competence
     * @return float
     */
    public function percentageActivityForCompetence(Competence $competence)
    {
        $activities = $this->analysisCollector->getLearningActivities()->filter(
            function (LearningActivityActing $activity) use ($competence) {
                return $activity->competence->first()->competence_id === $competence->competence_id;
            });
        if( $this->analysisCollector->getLearningActivities()->count() > 0) {
            return round(($activities->count() / $this->analysisCollector->getLearningActivities()->count()) * 100, 1);
        } else {
            return 0;
        }
    }

    /**
     * Get the percentage of activities with a person
     *
     * @param ResourcePerson $person
     * @return float
     */
    public function percentageActivityWithResourcePerson(ResourcePerson $person)
    {
        $activities = $this->analysisCollector->getLearningActivities()->filter(
            function (LearningActivityActing $activity) use ($person) {
                return $activity->resourcePerson->rp_id === $person->rp_id;
            });
        if($this->analysisCollector->getLearningActivities()->count() > 0) {
            return round(($activities->count() / $this->analysisCollector->getLearningActivities()->count()) * 100, 1);
        } else {
            return 0;
        }
    }

    /**
     * Get the percentage of activities with a theory
     *
     * @param ResourceMaterial $material
     * @return float
     */
    public function percentageActivityWithTheory(ResourceMaterial $material) {
        $activities = $this->analysisCollector->getLearningActivities()->filter(
            function(LearningActivityActing $activity) use ($material) {
                // There is no resourceMaterial for the "none" option, bypass a NullRef this way because in the analysisCollector we spoof the $material with a non-persisted material "none"
                if($activity->resourceMaterial === null) {
                    return $material->rm_id === null;
                }
                return $activity->resourceMaterial->rm_id === $material->rm_id;
            });

        if($this->analysisCollector->getLearningActivities()->count() > 0) {
            return round(($activities->count() / $this->analysisCollector->getLearningActivities()->count()) * 100, 1);
        } else {
            return 0;
        }
    }


    /**
     * Get the most often occurring combination of timeslot & learning goal for activities
     *
     * @return stdClass
     */
    public function mostOftenCombinationTimeslotLearningGoal() {
        $combo = new StdClass;
        $combo->timeslot = null;
        $combo->percentage = 0;
        $combo->learningGoal = null;


        // Loop over all the activities
        $this->analysisCollector->getLearningActivities()->each(function(LearningActivityActing $activity) use($combo) {
            // Find all activities with matching learning goal & timeslot
            $matchingActivities = $this->analysisCollector->getLearningActivities()->filter(function(LearningActivityActing $matchingActivity) use ($activity) {
                return ($activity->learningGoal->learninggoal_id === $matchingActivity->learningGoal->learninggoal_id &&
                $activity->timeslot->timeslot_id === $matchingActivity->timeslot->timeslot_id &&
                $activity !== $matchingActivity);
            });
            // Determine percentage of total
            if($this->analysisCollector->getLearningActivities()->count() > 0) {
                $percentage = ($matchingActivities->count() / $this->analysisCollector->getLearningActivities()->count());
            } else {
                $percentage = 0;
            }
            // Update combo->percentage if it's higher
            if($percentage >= $combo->percentage) {
                $combo->percentage = $percentage;
                $combo->timeslot = $activity->timeslot;
                $combo->learningGoal = $activity->learningGoal;

            }
        });

        // Nicer format
        $combo->percentage = round($combo->percentage * 100, 1);

        return $combo;
    }

    public function mostOftenCombinationTimeslotCompetence() {
        $combo = new StdClass;
        $combo->timeslot = null;
        $combo->percentage = 0;
        $combo->competence = null;

        // Loop over all the activities
        $this->analysisCollector->getLearningActivities()->each(function(LearningActivityActing $activity) use($combo) {
            // Find all activities with matching competence & timeslot
            $matchingActivities = $this->analysisCollector->getLearningActivities()->filter(function(LearningActivityActing $matchingActivity) use ($activity) {
                return ($activity->competence->first()->competence_label === $matchingActivity->competence->first()->competence_label &&
                    $activity->timeslot->timeslot_id === $matchingActivity->timeslot->timeslot_id &&
                    $activity !== $matchingActivity);
            });
            // Determine percentage of total
            if($this->analysisCollector->getLearningActivities()->count() > 0) {
                $percentage = ($matchingActivities->count() / $this->analysisCollector->getLearningActivities()->count());
            } else {
                $percentage = 0;
            }
            // Update combo->percentage if it's higher
            if($percentage >= $combo->percentage) {
                $combo->percentage = $percentage;
                $combo->timeslot = $activity->timeslot;
                $combo->competence = $activity->competence->first();
            }
        });

        // Nicer format
        $combo->percentage = round($combo->percentage * 100, 1);

        return $combo;
    }

    public function mostOftenCombinationLearningGoalCompetence() {
        $combo = new StdClass;
        $combo->learningGoal = null;
        $combo->percentage = 0;
        $combo->competence = null;

        // Loop over all the activities
        $this->analysisCollector->getLearningActivities()->each(function(LearningActivityActing $activity) use($combo) {
            // Find all activities with matching competence & learning goal
            $matchingActivities = $this->analysisCollector->getLearningActivities()->filter(function(LearningActivityActing $matchingActivity) use ($activity) {
                return ($activity->competence->first()->competence_label === $matchingActivity->competence->first()->competence_label &&
                    $activity->learningGoal->learninggoal_id === $matchingActivity->learningGoal->learninggoal_id &&
                    $activity !== $matchingActivity);
            });
            // Determine percentage of total
            if($this->analysisCollector->getLearningActivities()->count() > 0) {
                $percentage = ($matchingActivities->count() / $this->analysisCollector->getLearningActivities()->count());
            } else {
                $percentage = 0;
            }
            // Update combo->percentage if it's higher
            if($percentage >= $combo->percentage) {
                $combo->percentage = $percentage;
                $combo->learningGoal = $activity->learningGoal;
                $combo->competence = $activity->competence->first();
            }
        });

        // Nicer format
        $combo->percentage = round($combo->percentage * 100, 1);

        return $combo;
    }

    public function percentageLearningGoalWithoutMaterial(LearningGoal $learningGoal) {
        $activities = $this->analysisCollector->getLearningActivities();
        $activities = $activities->filter(function (LearningActivityActing $activity) use (&$learningGoal) {
            return (int)$activity->learninggoal_id == (int)$learningGoal->learninggoal_id;
        });
        $noTheory = $activities->filter(function(LearningActivityActing $activity) {
            return $activity->resourceMaterial === null;
        });
        if($noTheory->count() === 0) {
            return 0;
        }

        if ($this->analysisCollector->getLearningActivities()->count() > 0) {
            return round(($noTheory->count() / $this->analysisCollector->getLearningActivities()->count()) * 100, 1);
        } else {
            return 0;
        }
    }


}