<?php
class AnalyticsController {
    private $analyticsModel;
    private $websiteModel;
    
    public function __construct() {
        $this->analyticsModel = new Analytics();
        $this->websiteModel = new Website();
    }
    
    public function dashboard() {
        $totalWebsites = $this->analyticsModel->getTotalWebsites();
        $totalVisitors = $this->analyticsModel->getTotalVisitors();
        $totalPageViews = $this->analyticsModel->getTotalPageViews();
        $websiteStats = $this->analyticsModel->getWebsiteStats();
        $dailyStats = $this->analyticsModel->getDailyStats(null, 7);
        
        Flight::render('dashboard/index', [
            'title' => 'Dashboard',
            'totalWebsites' => $totalWebsites,
            'totalVisitors' => $totalVisitors,
            'totalPageViews' => $totalPageViews,
            'websiteStats' => $websiteStats,
            'dailyStats' => $dailyStats
        ]);
    }
        public function websiteAnalytics($id) {
        $website = $this->websiteModel->findById($id);
        
        if (!$website) {
            Flight::redirect('/websites?error=Site bulunamadı.');
            return;
        }
        
        try {
            $totalVisitors = $this->analyticsModel->getTotalVisitors($id);
            $totalPageViews = $this->analyticsModel->getTotalPageViews($id);
            $dailyStats = $this->analyticsModel->getDailyStats($id, 30);
            $popularPages = $this->analyticsModel->getPopularPages($id, 10);
            $browserStats = $this->analyticsModel->getBrowserStats($id);
            $osStats = $this->analyticsModel->getOSStats($id);
            
            // Ek istatistikler
            $deviceStats = $this->analyticsModel->getDeviceStats($id);
            $topReferrers = $this->analyticsModel->getTopReferrers($id, 5);
            $bounceRate = $this->analyticsModel->getBounceRate($id);
            $avgSessionDuration = $this->analyticsModel->getAverageSessionDuration($id);
            
            // Breadcrumb
            $breadcrumb = [
                ['title' => 'Web Siteleri', 'url' => '/websites', 'icon' => 'fas fa-globe'],
                ['title' => $website['name'], 'icon' => 'fas fa-chart-bar']
            ];
            
            Flight::render('analytics/website', [
                'title' => $website['name'] . ' - Analytics',
                'breadcrumb' => $breadcrumb,
                'website' => $website,
                'totalVisitors' => $totalVisitors,
                'totalPageViews' => $totalPageViews,
                'dailyStats' => $dailyStats,
                'popularPages' => $popularPages,
                'browserStats' => $browserStats,
                'osStats' => $osStats,
                'deviceStats' => $deviceStats,
                'topReferrers' => $topReferrers,
                'bounceRate' => $bounceRate,
                'avgSessionDuration' => $avgSessionDuration
            ]);
            
        } catch (Exception $e) {
            // Hata durumunda
            Flight::redirect('/websites?error=Analytics verilerinde hata oluştu: ' . $e->getMessage());
        }
    }
   
}
