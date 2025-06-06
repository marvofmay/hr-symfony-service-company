<?php

declare(strict_types=1);

namespace App\Module\System\Application\Console\FakeData;

use App\Module\Company\Domain\Enum\ContactTypeEnum;
use App\Module\System\Application\Console\FakeData\Data\Company as CompanyFakeData;
use App\Module\Company\Domain\Entity\Address;
use App\Module\Company\Domain\Entity\Company;
use App\Module\Company\Domain\Entity\Contact;
use App\Module\Company\Domain\Entity\Industry;;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:add-record-to-company-table')]
class AddRecordToCompanyTableCommand extends Command
{
    private const string DESCRIPTION = 'Add default company if not exists';
    private const string HELP = 'This command adds a default company based on predefined data if it does not already exist';
    private const string SUCCESS_MESSAGE = 'Company added successfully!';
    private const string INFO_ALREADY_EXISTS = 'Company already exists. No action taken.';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CompanyFakeData $companyFakeData,
    ) {
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
        $data = $this->companyFakeData->getDefaultData();

        $companyRepository = $this->entityManager->getRepository(Company::class);
        $existingCompany = $companyRepository->createQueryBuilder(Company::ALIAS)
            ->where(Company::ALIAS . '.fullName = :fullName')
            ->andWhere(Company::ALIAS . '.nip = :nip')
            ->andWhere(Company::ALIAS . '.regon = :regon')
            ->setParameters(new ArrayCollection([
                new Parameter('fullName', $data['fullName']),
                new Parameter('nip', $data['nip']),
                new Parameter('regon', $data['regon']),
            ]))
            ->getQuery()
            ->getOneOrNullResult();

        if ($existingCompany !== null) {
            $output->writeln(sprintf('<comment>%s</comment>', self::INFO_ALREADY_EXISTS));
            return Command::SUCCESS;
        }

        $industry = $this->entityManager->getRepository(Industry::class)->find($data['industryUUID']);

        $company = new Company();
        $company->setFullName($data['fullName']);
        $company->setShortName($data['shortName']);
        $company->setNip($data['nip']);
        $company->setRegon($data['regon']);
        $company->setDescription($data['description']);
        $company->setActive($data['active']);
        $company->setIndustry($industry);

        foreach ($data['phones'] as $phoneNumber) {
            $contact = new Contact();
            $contact->setType(ContactTypeEnum::PHONE->value);
            $contact->setData($phoneNumber);
            $contact->setActive(true);
            $contact->setCompany($company);
            $this->entityManager->persist($contact);
        }

        foreach ($data['emails'] as $emailAddress) {
            $contact = new Contact();
            $contact->setType(ContactTypeEnum::EMAIL->value);
            $contact->setData($emailAddress);
            $contact->setActive(true);
            $contact->setCompany($company);
            $this->entityManager->persist($contact);
        }

        foreach ($data['websites'] as $websiteUrl) {
            $contact = new Contact();
            $contact->setType(ContactTypeEnum::WEBSITE->value);
            $contact->setData($websiteUrl);
            $contact->setCompany($company);
            $this->entityManager->persist($contact);
        }

        $addressData = $data['address'];
        $address = new Address();
        $address->setStreet($addressData['street']);
        $address->setPostcode($addressData['postcode']);
        $address->setCity($addressData['city']);
        $address->setCountry($addressData['country']);
        $address->setActive($addressData['active']);
        $address->setCompany($company);

        $this->entityManager->persist($address);
        $this->entityManager->persist($company);
        $this->entityManager->flush();

        $output->writeln(sprintf('<info>%s</info>', self::SUCCESS_MESSAGE));

        return Command::SUCCESS;
    }
}