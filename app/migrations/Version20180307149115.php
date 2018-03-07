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

class Version20180307149115 extends AbstractMauticMigration
{
    /**
     * @param Schema $schema
     *
     * @throws SkipMigrationException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function preUp(Schema $schema)
    {
        if ($schema->hasTable(MAUTIC_TABLE_PREFIX.'billinginfo')) {
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
CREATE TABLE {$this->prefix}billinginfo (
  id int(11) NOT NULL AUTO_INCREMENT,
  is_published tinyint(1) NOT NULL,
  date_added datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  created_by int(11) DEFAULT NULL,
  created_by_user varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  date_modified datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  modified_by int(11) DEFAULT NULL,
  modified_by_user varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  checked_out datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
  checked_out_by int(11) DEFAULT NULL,
  checked_out_by_user varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  companyname varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  companyaddress varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  accountingemail varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
SQL;

        $this->addSql($sql);
    }
}
