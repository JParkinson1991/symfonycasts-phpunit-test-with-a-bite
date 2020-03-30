<?php
/**
 * @file
 * DefaultControllerTest.php
 */

namespace Tests\Functional\AppBundle\Controller;

use AppBundle\DataFixtures\ORM\LoadBasicParkData;
use AppBundle\DataFixtures\ORM\LoadSecurityData;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Throwable;

/**
 * Class DefaultControllerTest
 *
 * @package Tests\AppBundle\Controller
 */
class DefaultControllerTest extends WebTestCase
{
    /**
     * Request client
     *
     * Initialised in setUp()
     *
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    /**
     * Reference repository used for accessing fixture created entities.]
     *
     * Initialised in setUp()
     *
     * @var \Doctrine\Common\DataFixtures\ReferenceRepository
     */
    protected $fixtures;

    /**
     * Sets up the client for each test in this class
     *
     */
    protected function setUp()
    {
        $this->client = $this->makeClient();
        $this->fixtures = $this->loadFixtures([
            LoadBasicParkData::class,
            LoadSecurityData::class
        ])->getReferenceRepository();
    }

    /**
     * Dumps response content on failed tests
     *
     * @param \Throwable $t
     *
     * @throws \Throwable
     */
    protected function onNotSuccessfulTest(Throwable $t)
    {
        /** @noinspection ForgottenDebugOutputInspection */
        dump($this->client->getResponse()->getContent());

        parent::onNotSuccessfulTest($t);
    }

    /**
     * Tests enclosures are pulled from the database and rendered on the home
     * page correctly
     *
     */
    public function testEnclosuresAreShownOnTheHomepage()
    {
        /**
         * Do things the old fashioned way
         *
         * Clear the database, create the entities, and check against them
         *
        /// Clears the database without loading any fixtures data
        // Essentially, purge the database
        $this->loadFixtures([]);

        $em = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // Create the required entities for checking here
         */

        $crawler = $this->client->request('GET', '/');

        self::assertStatusCode(200, $this->client);

        // Grab DOM element with class
        // Expect 3 rows within the table
        $table = $crawler->filter('.table-enclosures');
        $this->assertCount(1, $table);
        $this->assertCount(3, $table->filter('tbody tr'));
    }

    /**
     * Tests that any enclosures shown on the homepage without securities
     * attached to them have a button displayed that can be used to raise and
     * alarm
     *
     */
    public function testThatThereIsAnAlarmButtonWithoutSecurity()
    {
        $crawler = $this->client->request('GET', '/');

        self::assertStatusCode(200, $this->client);

        /* @var \AppBundle\Entity\Enclosure $enclosure */
        $enclosure = $this->fixtures->getReference(LoadBasicParkData::CARNIVORE_ENCLOSURE);
        $selector = sprintf('.table-enclosures tr#enclosure-%s .button-alarm', $enclosure->getId());

        $this->assertGreaterThan(0, $crawler->filter($selector)->count());
    }

    /**
     * Tests a dinosaur can be added to a enclosure via form by provided its
     * specification
     *
     */
    public function testItGrowsADinosaurFromSpecification()
    {
        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/');

        self::assertStatusCode(200, $this->client);

        // Determine test values
        $enclosureId = $this->fixtures->getReference(LoadBasicParkData::HERBIVORE_ENCLOSURE)->getId();
        $dinosaurSpecification = 'Large herbivore';

        // Array keys of $form are input #names
        $form = $crawler->selectButton('Grow dinosaur')->form();
        $form['enclosure']->select($enclosureId);
        $form['specification']->setValue($dinosaurSpecification);

        $this->client->submit($form);

        $this->assertContains(
            sprintf(
                'Grew a %s in enclosure #%d',
                $dinosaurSpecification,
                $enclosureId
            ),
            $this->client->getResponse()->getContent(),
            '',
            true
        );
    }

}