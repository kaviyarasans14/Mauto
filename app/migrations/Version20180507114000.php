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

class Version20180507114000 extends AbstractMauticMigration
{
    /**
     * @param Schema $schema
     *
     * @throws SkipMigrationException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function preUp(Schema $schema)
    {
        if ($schema->hasTable(MAUTIC_TABLE_PREFIX.'stripecard')) {
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

        $this->addSql("CREATE TABLE stripecard (id INT AUTO_INCREMENT NOT NULL, customerid VARCHAR(255) DEFAULT NULL, last4digit VARCHAR(255) DEFAULT NULL, fingerprint VARCHAR(255) DEFAULT NULL, brand VARCHAR(255) DEFAULT NULL, updatedBy INT DEFAULT NULL, updatedByUser VARCHAR(255) DEFAULT NULL, updatedOn DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;");
    }
}
