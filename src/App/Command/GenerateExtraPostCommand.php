<?php

namespace App\Command;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use joshtronic\LoremIpsum;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateExtraPostCommand extends Command
{
    protected static $defaultName = 'app:generate-extra-post';
    protected static $defaultDescription = 'Run app:generate-extra-post';

    private EntityManagerInterface $em;
    private LoremIpsum $loremIpsum;

    public function __construct(EntityManagerInterface $em, LoremIpsum $loremIpsum, string $name = null)
    {
        parent::__construct($name);
        $this->em = $em;
        $this->loremIpsum = $loremIpsum;
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = new \DateTime();
        $title = 'Summary '. $date->format('Y-m-d');
        $content = $this->loremIpsum->paragraphs(1);
        
        $existing = $this->em->getRepository(Post::class)->findOneBy(['title' => $title]);
        
        if (!$existing instanceof Post) {
            $post = new Post();
            $post->setTitle($title);
            $post->setContent($content);

            $this->em->persist($post);
            $this->em->flush();

            $output->writeln('An extra post has been generated.');
        } else {
            $output->writeln('An extra post had been generated.');
        }
         
        return Command::SUCCESS;
    }
}
