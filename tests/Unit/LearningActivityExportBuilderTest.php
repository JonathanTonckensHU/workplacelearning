<?php

namespace Tests\Feature;

use App\LearningActivityActing;
use App\LearningActivityActingExportBuilder;
use Tests\TestCase;

class LearningActivityExportBuilderTest extends TestCase
{

    private function buildMock() {

        $mock = \Mockery::mock(LearningActivityActing::class);

        $mock->shouldReceive('getAttribute')->with('id')->andReturn('1');
        $mock->shouldReceive('getAttribute')->with('date')->andReturn('2017-10-10');
        $mock->shouldReceive('getAttribute')->with('situation')->andReturn('pressure');

        $mock->shouldReceive('getTimeslot')->times(1)->andReturn('1e lesuur');
        $mock->shouldReceive('getResourcePerson')->times(1)->andReturn('Medestudent');
        $mock->shouldReceive('getResourceMaterial')->times(1)->andReturn('Geen');
        $mock->shouldReceive('getAttribute')->with('lessonslearned')->andReturn('a lot');
        $mock->shouldReceive('getLearningGoal')->times(1)->andReturn('Leervraag 1');

        $learningGoal = new \StdClass;
        $learningGoal->description = "Description test";
        $mock->shouldReceive('getAttribute')->with('learningGoal')->times(1)->andReturn($learningGoal);

        $mock->shouldReceive('getAttribute')->with('support_wp')->andReturn('support from wp');
        $mock->shouldReceive('getAttribute')->with('support_ed')->andReturn('support from ed');

        $competenceObject = new \StdClass;
        $competenceObject->competence_label = "Interpersoonlijk";
        $mock->shouldReceive('getCompetencies')->times(1)->andReturn($competenceObject);
        $mock->shouldReceive('getAttribute')->with('laa_id')->andReturn('1');

        $mock->shouldReceive('getAttribute')->with('evidence_filename')->andReturn(null);

        return $mock;


    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetJson()
    {
        $exporter = new LearningActivityActingExportBuilder(collect([$this->buildMock()]));
        $json = $exporter->getJson();

        $this->assertTrue(is_string($json), "Export is not a string, therefore not JSON");

        $decoded = json_decode($json);

        $mapping = [
            "id" => 1,
            "date" => "10-10-2017",
            "situation" => "pressure",
            "timeslot" => "1e lesuur",
            "resourcePerson" => "Medestudent",
            "resourceMaterial" => "Geen",
            "lessonsLearned" => "a lot",
            "learningGoal" => "Leervraag 1",
            "learningGoalDescription" => "Description test",
            "supportWp" => "support from wp",
            "supportEd" => "support from ed",
            "competence" => "Interpersoonlijk",
            "url" => route('process-acting-edit', ["id" => 1])
        ];


        foreach($mapping as $field => $value) {
            $this->assertEquals($value, $decoded[0]->{$field}, "{$field}: expected({$value}) got({$decoded[0]->{$field}})");
        }


    }
}
