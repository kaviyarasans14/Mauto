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

class Version20180320181515 extends AbstractMauticMigration
{
    /**
     * @param Schema $schema
     *
     * @throws SkipMigrationException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function preUp(Schema $schema)
    {
        if ($schema->hasTable(MAUTIC_TABLE_PREFIX.'kyc')) {
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
CREATE TABLE {$this->prefix}kyc (
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
  industry varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  usercount varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  yearsactive varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  subscribercount varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  subscribersource varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  emailcontent varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  previoussoftware varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  knowus varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  others varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  conditionsagree int(11) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
SQL;

        $this->addSql($sql);

        $usql = <<<SQL
CREATE TABLE {$this->prefix}user_preference (
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
  property varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  userid int(11) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
SQL;
        $this->addSql($usql);

        $this->addSql("ALTER TABLE {$this->prefix}billinginfo ADD postalcode INT(11)");
        $this->addSql("ALTER TABLE {$this->prefix}billinginfo ADD city VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE {$this->prefix}billinginfo ADD state VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE {$this->prefix}billinginfo ADD country VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE {$this->prefix}billinginfo ADD gstnumber VARCHAR(255) DEFAULT NULL");
    }
}
