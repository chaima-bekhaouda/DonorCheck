<?php
require_once __DIR__ . '/../models/DonorModel.php';

class DonorController
{
    private DonorModel $donorModel;

    public function __construct(PDO $pdo)
    {
        $this->donorModel = new DonorModel($pdo);
    }

    public function index(): void
    {
        require_once __DIR__ . '/../views/index_view.php';
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $donor = new DonorDTO(
                (int)null,
                $_POST['name'],
                $_POST['first_name'],
                $_POST['email'],
                $_POST['phone_number'],
                $_POST['sex'],
                $_POST['age'],
                $_POST['weight'],
                isset($_POST['temporary_exclusion']),
                $_POST['reason_temporary_exclusion'],
                isset($_POST['permanent_exclusion']),
                $_POST['reason_permanent_exclusion'],
                $_POST['last_blood_donation_date'],
                $_POST['last_plasma_donation_date'],
            );
            $this->donorModel->save($donor);
            header('Location: index.php');
            exit();
        }

        require_once __DIR__ . '/../views/add_view.php';
    }

    /**
     * @throws Exception
     */
    public function search(): void
    {
        $attributes = $_GET['criteria']; // ['name', 'first_name'...]
        $searches = $_GET['value']; // ['John', 'Doe'...]

        $criteria = array_combine($attributes, $searches);

        $donors = $this->donorModel->search($criteria);

        // Create an array to hold the canDonateAgain data for each donor
        $donorsCanDonate = [];

        // Loop through each donor and get their canDonateAgain data
        foreach ($donors as $donor) {
            $donorsCanDonate[$donor->getId()] = $this->donorModel->canDonateAgain($donor->getId());
        }

        require_once __DIR__ . '/../views/search_view.php';
    }

    /**
     * @throws Exception
     */
    public function donor_details(): void
    {
        $donorId = $_GET['id'];
        $donor = $this->donorModel->getById($donorId);
        $donorCanDonate = $this->donorModel->canDonateAgain($donorId);

        require_once __DIR__ . '/../views/donor_details_view.php';
    }
}
