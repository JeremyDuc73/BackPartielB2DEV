<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240408134616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image ADD product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C53D045F4584665A ON image (product_id)');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT fk_d34a04ad3da5256d');
        $this->addSql('DROP INDEX uniq_d34a04ad3da5256d');
        $this->addSql('ALTER TABLE product DROP image_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE product ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT fk_d34a04ad3da5256d FOREIGN KEY (image_id) REFERENCES image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_d34a04ad3da5256d ON product (image_id)');
        $this->addSql('ALTER TABLE image DROP CONSTRAINT FK_C53D045F4584665A');
        $this->addSql('DROP INDEX UNIQ_C53D045F4584665A');
        $this->addSql('ALTER TABLE image DROP product_id');
    }
}
