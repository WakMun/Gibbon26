<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

namespace Gibbon\Forms\Builder\Fields;

use Gibbon\Domain\User\UserGateway;
use Gibbon\Forms\Form;
use Gibbon\Forms\Layout\Row;
use Gibbon\Forms\Builder\AbstractFieldGroup;
use Gibbon\Forms\Builder\FormBuilderInterface;
use Gibbon\Domain\System\SettingGateway;

class Parent1Fields extends AbstractFieldGroup
{
    protected $userGateway;
    protected $uniqueEmailAddress;

    public function __construct(SettingGateway $settingGateway, UserGateway $userGateway)
    {
        $this->uniqueEmailAddress = $settingGateway->getSettingByScope('User Admin', 'uniqueEmailAddress');
        $this->userGateway = $userGateway;

        $this->fields = [
            'headingParentGuardian1' => [
                'label' => __('Parent/Guardian')." 1",
                'type'  => 'heading',
            ],
            'headingParentGuardian1PersonalData' => [
                'label' => __('Parent/Guardian')." 1 ".__('Personal Data'),
                'type'  => 'subheading',
            ],
            'parent1title' => [
                'label'    => __('Title'),
                'required' => 'Y',
                'prefill'  => 'Y',
            ],
            'parent1surname' => [
                'label'       => __('Surname'),
                'description' => __('Family name as shown in ID documents.'),
                'required'    => 'X',
                'prefill'     => 'Y',
            ],
            'parent1firstName' => [
                'label'    => __('First Name'),
                'description' => __('First name as shown in ID documents.'),
                'required' => 'Y',
                'prefill'  => 'Y',
            ],
            'parent1preferredName' => [
                'label'    => __('Preferred Name'),
                'description' => __('Most common name, alias, nickname, etc.'),
                'required' => 'X',
                'prefill'  => 'Y',
            ],
            'parent1officialName' => [
                'label'    => __('Official Name'),
                'description' => __('Full name as shown in ID documents.'),
                'required' => 'Y',
                'prefill'  => 'Y',
            ],
            'parent1nameInCharacters' => [
                'label'    => __('Name In Characters'),
                'description' => __('Chinese or other character-based name.'),
                'prefill'  => 'Y',
            ],
            'parent1gender' => [
                'label' => __('Gender'),
                'required' => 'Y',
                'prefill'  => 'Y',
                'type'     => 'gender',
            ],
            'parent1relationship' => [
                'label'    => __('Relationship'),
                'required' => 'Y',
                'prefill'  => 'Y',
            ],
            'headingParentGuardian1PersonalBackground' => [
                'label' => __('Parent/Guardian')." 1 ".__('Personal Background'),
                'type'  => 'subheading',
            ],
            'parent1languageFirst' => [
                'label' => __('First Language'),
                'description' => __('Student\'s native/first/mother language.'),
                'prefill'  => 'Y',
            ],
            'parent1languageSecond' => [
                'label' => __('Second Language'),
                'prefill'  => 'Y',
            ],
            'headingParentGuardian1Contact' => [
                'label' => __('Parent/Guardian')." 1 ".__('Contact'),
                'type'  => 'subheading',
            ],
            'parent1email' => [
                'label'    => __('Email'),
                'type'     => 'email',
                'required' => 'Y',
                'prefill'  => 'Y',
            ],
            'parent1phone' => [
                'label'       => __('Phone'),
                'description' => __('Type, country code, number.'),
                'type'        => 'phone',
                'prefill'     => 'Y',
                'acquire'     => ['parent1phone1' => 'varchar', 'parent1phone1Type' => 'varchar', 'parent1phone1CountryCode' => 'varchar','parent1phone2' => 'varchar', 'parent1phone2Type' => 'varchar', 'parent1phone2CountryCode' => 'varchar'],
            ],
            'headingParentGuardian1Employment' => [
                'label' => __('Parent/Guardian')." 1 ".__('Employment'),
                'type'  => 'subheading',
            ],
            'parent1profession' => [
                'label' => __('Profession'),
                'required' => 'Y',
                'prefill'  => 'Y',
            ],
            'parent1employer' => [
                'label' => __('Employer'),
                'prefill'  => 'Y',
            ],
        ];
    }

    public function getDescription() : string
    {
        return __('Parent fields enable the creation of parent users once an application has been accepted.');
    }

    public function addFieldToForm(FormBuilderInterface $formBuilder, Form $form, array $field) : Row
    {
        $required = $this->getRequired($formBuilder, $field);

        $row = $form->addRow();

        // PARENT 1: Already logged in, record gibbonPersonID
        if ($formBuilder->hasConfig('gibbonPersonID') && $field['fieldName'] != 'parent1relationship') {

            if ($field['fieldName'] == 'parent1surname') {
                $parent = $this->userGateway->getByID($formBuilder->getConfig('gibbonPersonID'));

                $form->addHiddenValue('parent1email', $parent['email'] ?? '');
                $form->addHiddenValue('parent1gibbonPersonID', $parent['gibbonPersonID'] ?? '');

                $row->addLabel('parent1username', __('Username'))->description(__('System login ID.'));
                $row->addTextField('parent1username')->setValue($parent['username'] ?? '')->maxLength(30)->readOnly();

                $row = $form->addRow();
                $row->addLabel('parent1surname', __('Surname'))->description(__('Family name as shown in ID documents.'));
                $row->addTextField('parent1surname')->setValue($parent['surname'] ?? '')->maxLength(30)->readOnly();

                $row = $form->addRow();
                $row->addLabel('parent1preferredName', __('Preferred Name'))->description(__('Most common name, alias, nickname, etc.'));
                $row->addTextField('parent1preferredName')->setValue($parent['preferredName'] ?? '')->maxLength(30)->readOnly();
                    
            } else {
                $row->addClass('hidden');
            }
            
            return $row;
        }

        switch ($field['fieldName']) {
            // PARENT 1 PERSONAL DATA
            case 'parent1title':
                $row->addLabel('parent1title', __($field['label']))->description(__($field['description']));
                $row->addSelectTitle('parent1title')->required($required);
                break;

            case 'parent1surname':
                $row->addLabel('parent1surname', __($field['label']))->description(__($field['description']));
                $row->addTextField('parent1surname')->required($required)->maxLength(60);
                break;

            case 'parent1firstName':
                $row->addLabel('parent1firstName', __($field['label']))->description(__($field['description']));
                $row->addTextField('parent1firstName')->required($required)->maxLength(60);
                break;

            case 'parent1preferredName':
                $row->addLabel('parent1preferredName', __($field['label']))->description(__($field['description']));
                $row->addTextField('parent1preferredName')->required($required)->maxLength(60);
                break;

            case 'parent1officialName':
                $row->addLabel('parent1officialName', __($field['label']))->description(__($field['description']));
                $row->addTextField('parent1officialName')->required($required)->maxLength(150)->setTitle(__('Please enter full name as shown in ID documents'));
                break;

            case 'parent1nameInCharacters':
                $row->addLabel('parent1nameInCharacters', __($field['label']))->description(__($field['description']));
                $row->addTextField('parent1nameInCharacters')->required($required)->maxLength(60);
                break;

            case 'parent1gender':
                $row->addLabel('parent1gender', __($field['label']))->description(__($field['description']));
                $row->addSelectGender('parent1gender')->required($required);
                break;

            case 'parent1relationship':
                $row->addLabel('parent1relationship', __($field['label']))->description(__($field['description']));
                $row->addSelectRelationship('parent1relationship')->required($required);
                break;

            // PARENT1 BACKGROUND
            case 'parent1languageFirst':
                $row->addLabel('parent1languageFirst', __($field['label']))->description(__($field['description']));
                $row->addSelectLanguage('parent1languageFirst')->required($required);
                break;
        
            case 'parent1languageSecond':
                $row->addLabel('parent1languageSecond', __($field['label']))->description(__($field['description']));
                $row->addSelectLanguage('parent1languageSecond')->placeholder('')->required($required);
                break;

            // PARENT1 CONTACT
            case 'parent1email':
                $row->addLabel('parent1email', __($field['label']))->description(__($field['description']));
                $email = $row->addEmail('parent1email')->required($required);
                if ($this->uniqueEmailAddress == 'Y') {
                    $email->uniqueField('./publicRegistrationCheck.php', ['fieldName' => 'email']);
                }
                break;

            case 'parent1phone':
                $colGroup = $row->addColumn()->setClass('flex-col w-full justify-between items-start');
                for ($i = 1; $i < 3; ++$i) {
                    $col = $colGroup->addColumn()->setClass('flex flex-row justify-between');
                    $col->addLabel('parent1phone'.$i, __('Phone').' '.$i)->description(__($field['description']));
                    $col->addPhoneNumber('parent1phone'.$i)->required($required);
                }
                break;

            // PARENT1 EMPLOYMENT
            case 'parent1profession':
                $row->addLabel('parent1profession',__($field['label']))->description(__($field['description']));
                $row->addTextField('parent1profession')->maxLength(90)->required($required);
                break;

            case 'parent1employer':
                $row->addLabel('parent1employer',__($field['label']))->description(__($field['description']));
                $row->addTextField('parent1employer')->maxLength(90)->required($required);
                break;
        }

        return $row;
    }

    public function shouldValidate(FormBuilderInterface $formBuilder, array &$data, string $fieldName)
    {
        if ($formBuilder->hasConfig('gibbonPersonID') && $fieldName != 'parent1relationship') return false;
        
        return true;
    }
}