<?php

namespace Doctrine\Website\Twig;

use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectVersion;
use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleTest;

class MainExtension extends Twig_Extension
{
    /** @var array */
    private $teamMembers;

    public function __construct(array $teamMembers)
    {
        $this->teamMembers = $teamMembers;
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('get_asset_url', [$this, 'getAssetUrl']),
            new Twig_SimpleFunction('get_team_members', [$this, 'getTeamMembers']),
            new Twig_SimpleFunction('get_project_team_members', [$this, 'getProjectTeamMembers']),
            new Twig_SimpleFunction('get_gravatar_url', [$this, 'getGravatarUrl'])
        ];
    }

    public function getAssetUrl(string $path, string $siteUrl)
    {
        return $siteUrl.$path.'?'.$this->getAssetCacheBuster($path);
    }

    public function getTeamMembers() : array
    {
        $teamMembers = [];

        foreach ($this->teamMembers as $key => $teamMember) {
            $key = $teamMember['name'] ?? $key;

            $teamMembers[$key] = $teamMember;
        }

        ksort($teamMembers);

        return $teamMembers;
    }

    public function getProjectTeamMembers(Project $project) : array
    {
        return array_filter($this->getTeamMembers(), function(array $teamMember) use ($project) {
            return in_array($project->getSlug(), $teamMember['projects'] ?? []);
        });
    }

    private function getAssetCacheBuster(string $path) : string
    {
        $assetPath = realpath(__DIR__.'/../../../../../source/'.$path);

        return substr(md5(file_get_contents($assetPath)), 0, 6);
    }
}
