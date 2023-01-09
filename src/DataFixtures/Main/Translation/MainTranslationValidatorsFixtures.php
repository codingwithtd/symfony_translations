<?php

namespace App\DataFixtures\Main\Translation;

use App\DataFixtures\Main\Filter\Translation\MainFilterTranslationCategoriesFixtures;
use App\Entity\Main\Translation\Filtrate\MainTranslationFiltrateValidators;
use App\Entity\Main\Translation\MainTranslationValidators;
use App\Utilities\ConvertCSVToArray;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MainTranslationValidatorsFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private ConvertCSVToArray $convertCSVToArray;
    private string $filename;

    public function __construct(ConvertCSVToArray $convertCSVToArray)
    {
        $this->convertCSVToArray = $convertCSVToArray;
        $this->filename = 'src/DataFixtures/_data_csv/main/translation/app_translation_validators.csv';
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadFixtures($manager);
    }

    public function loadFixtures(ObjectManager $manager): void
    {
        foreach ($this->getFixtureData() as [ $is_active, $filters_categories, $translation_key, $key_value, $is_pri_trans, $primary_translation, $is_sec_trans, $secondary_translation, $is_pri_desc, $primary_description, $is_sec_desc, $secondary_description ]) {

            $str = new MainTranslationValidators();
            $str->setIsActive($is_active);
            $str->setTranslationKey($translation_key);
            $str->setMainFilterTranslationCategories($filters_categories);
            $str->setCreatedBy('WebAdmin');
            $str->setCreatedAt(new \DateTime('now'));

            $manager->persist($str);

            $str_pri = new MainTranslationFiltrateValidators();
            $str_pri->setTranslationLocale('en_za');
            $str_pri->setTranslationDescription($primary_translation);
            $str_pri->setMainTranslationValidators($str);

            $manager->persist($str_pri);

            $str_sec = new MainTranslationFiltrateValidators();
            $str_sec->setTranslationLocale('af_za');
            $str_sec->setTranslationDescription($secondary_translation);
            $str_sec->setMainTranslationValidators($str);

            $manager->persist($str_sec);

        }

        $manager->flush();
    }

    public function getFixtureData(): array{

        $file = $this->convertCSVToArray->convert($this->filename);

        $data = [];

        foreach ($file as $key => $result)
            //$is_active, $translation_key, $key_value, $is_primary_translation, $pri_trans, $is_secondary_translation, $sec_trans, $is_pri_desc, $primary_description, $is_sec_desc, $secondary_description
            $data [] = [
                $result['is_act'],//$is_active
                $this->getReference($result['filters_categories']),//$filters_categories
                $result['translation_key'], //$translation_key
                $result['value'], //$key_value
                $result['pri_trans'], //$is_pri_trans
                $result['primary_translation'], //$primary_translation
                $result['sec_trans'], //$is_sec_trans
                $result['secondary_translation'], //$secondary_translation
                $result['pri_desc'], //$is_pri_desc
                $result['primary_description'],//$primary_description
                $result['sec_desc'], //$is_sec_desc
                $result['secondary_description'], //$secondary_description
            ];
        return $data;
    }

    public function getDependencies(): array
    {
        return [
            MainFilterTranslationCategoriesFixtures::class
        ];
    }

    public static function getGroups(): array
    {
        return ['main_translation'];
    }
}
