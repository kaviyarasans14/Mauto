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

class Version20180401144610 extends AbstractMauticMigration
{
    /**
     * @param Schema $schema
     *
     * @throws SkipMigrationException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function preUp(Schema $schema)
    {
        if ($schema->hasTable(MAUTIC_TABLE_PREFIX.'paymenthistory')) {
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

        $this->addSql("CREATE TABLE {$this->prefix}paymenthistory (id INT AUTO_INCREMENT NOT NULL, orderid VARCHAR(255) DEFAULT NULL, paymentid VARCHAR(255) DEFAULT NULL, paymentstatus VARCHAR(255) DEFAULT NULL, provider VARCHAR(255) DEFAULT NULL, currency VARCHAR(255) DEFAULT NULL, amount VARCHAR(255) DEFAULT NULL, beforecredits VARCHAR(255) DEFAULT NULL, addedcredits VARCHAR(255) DEFAULT NULL, aftercredits VARCHAR(255) DEFAULT NULL, validitytill VARCHAR(255) DEFAULT NULL, planname VARCHAR(255) DEFAULT NULL, planlabel VARCHAR(255) DEFAULT NULL, createdBy INT DEFAULT NULL, createdByUser VARCHAR(255) DEFAULT NULL, createdOn DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;");
    }
}
