<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\FakeData;

use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Entity\Department;
use App\Module\Company\Domain\Enum\ContactTypeEnum;
use App\Module\System\Application\Console\FakeData\Data\Company as CompanyFakeData;
use App\Module\System\Application\Console\FakeData\Data\Department as DepartmentFakeData;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:add-record-to-department-table')]
class AddRecordToDepartmentTableCommand extends Command
{
    private const string DESCRIPTION = 'Add default company if not exists';
    private const string HELP = 'This command adds a default company based on predefined data if it does not already exist';
    private const string SUCCESS_MESSAGE = 'Company added successfully!';
    private const string COMPANY_NOT_EXISTS = 'Company not exists. No action taken.';
    private const string DEPARTMENT_ALREADY_EXISTS = 'Department already exists. No action taken.';

    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly DepartmentFakeData $departmentFakeData)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::DESCRIPTION)
            ->setHelp(self::HELP);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->departmentFakeData->getDefaultData();

        $companyRepository = $this->entityManager->getRepository(Company::class);
        $existingCompany = $companyRepository->createQueryBuilder(Company::ALIAS)
            ->where(Company::ALIAS.'.fullName = :fullName')
            ->setParameters(new ArrayCollection([
                new Parameter('fullName', CompanyFakeData::COMPANY_NAME_FUTURE_TECHNOLOGY),
            ]))
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $existingCompany) {
            $output->writeln(sprintf('<comment>%s</comment>', self::COMPANY_NOT_EXISTS));

            return Command::SUCCESS;
        }

        $existingDepartment = null;
        if (isset($data['departmentUUID']) && is_string($data['departmentUUID'])) {
            $departmentRepository = $this->entityManager->getRepository(Department::class);
            $existingDepartment = $departmentRepository->createQueryBuilder(Department::ALIAS)
                ->where(Company::ALIAS.'.uuid = :uuid')
                ->setParameters(new ArrayCollection([
                    new Parameter('uuid', $data['departmentUUID']),
                ]))
                ->getQuery()
                ->getOneOrNullResult();

            if (null !== $existingDepartment) {
                $output->writeln(sprintf('<comment>%s</comment>', self::DEPARTMENT_ALREADY_EXISTS));

                return Command::SUCCESS;
            }
        }

        $department = new Department();
        $department->setName($data['name']);
        $department->setDescription($data['description']);
        $department->setActive($data['active']);
        $department->setCompany($existingCompany);
        if ($existingDepartment instanceof Department) {
            $department->setParentDepartment($existingDepartment);
        }

        foreach ($data['phones'] as $phoneNumber) {
            $contact = new Contact();
            $contact->setType(ContactTypeEnum::PHONE->value);
            $contact->setData($phoneNumber);
            $contact->setActive(true);
            $contact->setDepartment($department);
            $this->entityManager->persist($contact);
        }

        foreach ($data['emails'] as $emailAddress) {
            $contact = new Contact();
            $contact->setType(ContactTypeEnum::EMAIL->value);
            $contact->setData($emailAddress);
            $contact->setActive(true);
            $contact->setDepartment($department);
            $this->entityManager->persist($contact);
        }

        foreach ($data['websites'] as $websiteUrl) {
            $contact = new Contact();
            $contact->setType(ContactTypeEnum::WEBSITE->value);
            $contact->setData($websiteUrl);
            $contact->setDepartment($department);
            $this->entityManager->persist($contact);
        }

        $addressData = $data['address'];
        $address = new Address();
        $address->setStreet($addressData['street']);
        $address->setPostcode($addressData['postcode']);
        $address->setCity($addressData['city']);
        $address->setCountry($addressData['country']);
        $address->setActive($addressData['active']);
        $address->setDepartment($department);

        $this->entityManager->persist($address);
        $this->entityManager->persist($department);
        $this->entityManager->flush();

        $output->writeln(sprintf('<info>%s</info>', self::SUCCESS_MESSAGE));

        return Command::SUCCESS;
    }
}
