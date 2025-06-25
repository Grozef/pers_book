<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250625133014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE astonishing_video (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, author_first_name VARCHAR(255) NOT NULL, author_last_name VARCHAR(255) NOT NULL, rating INT DEFAULT NULL, is_public TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, publish_date DATE DEFAULT NULL, publisher VARCHAR(255) DEFAULT NULL, filepath VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE fierce_publisher (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, tel VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE fierce_publisher_astonishing_video (fierce_publisher_id INT NOT NULL, astonishing_video_id INT NOT NULL, INDEX IDX_BC0EC792A2C7203E (fierce_publisher_id), INDEX IDX_BC0EC792639243FF (astonishing_video_id), PRIMARY KEY(fierce_publisher_id, astonishing_video_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE fierce_publisher_stunning_image (fierce_publisher_id INT NOT NULL, stunning_image_id INT NOT NULL, INDEX IDX_A4F09259A2C7203E (fierce_publisher_id), INDEX IDX_A4F0925921073B9 (stunning_image_id), PRIMARY KEY(fierce_publisher_id, stunning_image_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE fierce_publisher_wonderfull_book (fierce_publisher_id INT NOT NULL, wonderfull_book_id INT NOT NULL, INDEX IDX_8DB96736A2C7203E (fierce_publisher_id), INDEX IDX_8DB96736C7336A1F (wonderfull_book_id), PRIMARY KEY(fierce_publisher_id, wonderfull_book_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE stunning_image (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, author_first_name VARCHAR(255) NOT NULL, author_last_name VARCHAR(255) NOT NULL, rating INT DEFAULT NULL, is_public TINYINT(1) NOT NULL, price DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, published_date DATE DEFAULT NULL, publisher VARCHAR(255) DEFAULT NULL, filepath VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, user_info_id INT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649586DFF2 (user_info_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_info (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, tel VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE wonderfull_book (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, author_first_name VARCHAR(255) NOT NULL, author_last_name VARCHAR(255) NOT NULL, rating INT DEFAULT NULL, is_public TINYINT(1) NOT NULL, price DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, published_date DATE DEFAULT NULL, genre VARCHAR(255) DEFAULT NULL, publisher VARCHAR(255) DEFAULT NULL, isbn VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE fierce_publisher_astonishing_video ADD CONSTRAINT FK_BC0EC792A2C7203E FOREIGN KEY (fierce_publisher_id) REFERENCES fierce_publisher (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE fierce_publisher_astonishing_video ADD CONSTRAINT FK_BC0EC792639243FF FOREIGN KEY (astonishing_video_id) REFERENCES astonishing_video (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE fierce_publisher_stunning_image ADD CONSTRAINT FK_A4F09259A2C7203E FOREIGN KEY (fierce_publisher_id) REFERENCES fierce_publisher (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE fierce_publisher_stunning_image ADD CONSTRAINT FK_A4F0925921073B9 FOREIGN KEY (stunning_image_id) REFERENCES stunning_image (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE fierce_publisher_wonderfull_book ADD CONSTRAINT FK_8DB96736A2C7203E FOREIGN KEY (fierce_publisher_id) REFERENCES fierce_publisher (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE fierce_publisher_wonderfull_book ADD CONSTRAINT FK_8DB96736C7336A1F FOREIGN KEY (wonderfull_book_id) REFERENCES wonderfull_book (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649586DFF2 FOREIGN KEY (user_info_id) REFERENCES user_info (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE fierce_publisher_astonishing_video DROP FOREIGN KEY FK_BC0EC792A2C7203E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE fierce_publisher_astonishing_video DROP FOREIGN KEY FK_BC0EC792639243FF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE fierce_publisher_stunning_image DROP FOREIGN KEY FK_A4F09259A2C7203E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE fierce_publisher_stunning_image DROP FOREIGN KEY FK_A4F0925921073B9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE fierce_publisher_wonderfull_book DROP FOREIGN KEY FK_8DB96736A2C7203E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE fierce_publisher_wonderfull_book DROP FOREIGN KEY FK_8DB96736C7336A1F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649586DFF2
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE astonishing_video
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE fierce_publisher
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE fierce_publisher_astonishing_video
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE fierce_publisher_stunning_image
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE fierce_publisher_wonderfull_book
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE stunning_image
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `user`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_info
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE wonderfull_book
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
