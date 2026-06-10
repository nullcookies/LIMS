<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Instrument;
use App\Entity\Sample;
use App\Entity\SampleTest;
use App\Entity\TestMethod;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/reports')]
class ReportController extends AbstractController
{
    #[Route('/sample-activity', name: 'report_sample_activity', methods: ['GET'])]
    public function sampleActivity(EntityManagerInterface $em): JsonResponse
    {
        $samples = $em->getRepository(Sample::class)->findAll();
        $daily = [];
        $statusCount = ['registered' => 0, 'in_progress' => 0, 'completed' => 0, 'approved' => 0, 'rejected' => 0];
        foreach ($samples as $s) {
            $day = $s->getCreatedAt()->format('Y-m-d');
            $daily[$day] = ($daily[$day] ?? 0) + 1;
            $st = $s->getStatus();
            if (isset($statusCount[$st])) $statusCount[$st]++;
        }
        ksort($daily);
        $dailyData = [];
        foreach ($daily as $date => $count) {
            $dailyData[] = ['date' => $date, 'count' => $count];
        }
        $statusData = [];
        foreach ($statusCount as $k => $v) {
            $statusData[] = ['status' => $k, 'count' => $v];
        }
        return $this->json(['daily' => $dailyData, 'statusBreakdown' => $statusData]);
    }

    #[Route('/test-results', name: 'report_test_results', methods: ['GET'])]
    public function testResults(EntityManagerInterface $em): JsonResponse
    {
        $tests = $em->getRepository(SampleTest::class)->findAll();
        $byMethod = [];
        $statusCount = ['pending' => 0, 'in_progress' => 0, 'completed' => 0, 'approved' => 0, 'rejected' => 0];
        $total = count($tests);
        foreach ($tests as $t) {
            $method = $t->getTestMethod()->getName();
            $byMethod[$method] = ($byMethod[$method] ?? 0) + 1;
            $st = $t->getStatus();
            if (isset($statusCount[$st])) $statusCount[$st]++;
        }
        $methodData = [];
        foreach ($byMethod as $name => $count) {
            $methodData[] = ['method' => $name, 'count' => $count];
        }
        $statusData = [];
        foreach ($statusCount as $k => $v) {
            $statusData[] = ['status' => $k, 'count' => $v];
        }
        return $this->json(['total' => $total, 'byMethod' => $methodData, 'byStatus' => $statusData]);
    }

    #[Route('/customer-summary', name: 'report_customer_summary', methods: ['GET'])]
    public function customerSummary(EntityManagerInterface $em): JsonResponse
    {
        $customers = $em->getRepository(Customer::class)->findAll();
        $data = [];
        foreach ($customers as $c) {
            $data[] = [
                'name' => $c->getName(),
                'sampleCount' => count($c->getSamples()),
            ];
        }
        return $this->json($data);
    }

    #[Route('/instrument-status', name: 'report_instrument_status', methods: ['GET'])]
    public function instrumentStatus(EntityManagerInterface $em): JsonResponse
    {
        $instruments = $em->getRepository(Instrument::class)->findAll();
        $data = [];
        $now = new \DateTimeImmutable();
        foreach ($instruments as $inst) {
            $cal = $inst->getLastCalibration();
            $overdue = $cal ? $cal->modify('+365 days') < $now : true;
            $data[] = [
                'name' => $inst->getName(),
                'model' => $inst->getModel(),
                'serialNumber' => $inst->getSerialNumber(),
                'lastCalibration' => $cal?->format('Y-m-d'),
                'overdue' => $overdue,
            ];
        }
        return $this->json($data);
    }
}
