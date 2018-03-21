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

class Version20180321110540 extends AbstractMauticMigration
{
    /**
     * @param Schema $schema
     *
     * @throws SkipMigrationException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function preUp(Schema $schema)
    {
        if ($schema->hasTable(MAUTIC_TABLE_PREFIX.'licenseinfo')) {
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

        $sql = <<<SQL
CREATE TABLE {$this->prefix}`licenseinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total_record_count` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `actual_record_count` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_email_count` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `actual_email_count` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active_user_count` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_user_count` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `licensed_days` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `license_start_date` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `license_end_date` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_attachement_size` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `actual_attachement_size` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bounce_count` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `spam_count` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `app_status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
SQL;

        $this->addSql($sql);
    }
}
