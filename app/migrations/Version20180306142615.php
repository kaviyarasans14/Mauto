<?php
/**
 * Created by PhpStorm.
 * User: cratio
 * Date: 23/2/18
 * Time: 11:21 AM.
 */

namespace Mautic\Migrations;

use Doctrine\DBAL\Migrations\SkipMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Mautic\CoreBundle\Doctrine\AbstractMauticMigration;

class Version20180306142615 extends AbstractMauticMigration
{
    /**
     * @param Schema $schema
     *
     * @throws SkipMigrationException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function preUp(Schema $schema)
    {
        if ($schema->getTable(MAUTIC_TABLE_PREFIX.'emails')->hasColumn('failure_count')) {
            throw new SkipMigrationException('Schema includes this migration');
        }
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("ALTER TABLE {$this->prefix}reports ADD failure_count INT(10) DEFAULT NULL");
        $this->addSql("ALTER TABLE {$this->prefix}reports ADD unsubscribe_count INT(10) DEFAULT NULL");
        $this->addSql("ALTER TABLE {$this->prefix}reports ADD bounce_count INT(10) DEFAULT NULL");
        $this->addSql("ALTER TABLE {$this->prefix}reports ADD variant_failure_count INT(10) DEFAULT NULL");
        $this->addSql("ALTER TABLE {$this->prefix}reports ADD variant_unsubscribe_count INT(10) DEFAULT NULL");
        $this->addSql("ALTER TABLE {$this->prefix}reports ADD variant_bounce_count INT(10) DEFAULT NULL");
    }
}
