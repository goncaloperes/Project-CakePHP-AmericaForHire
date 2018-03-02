<?php

class JobsController extends  AppController{

    public $name = 'Jobs';

    /*
     * Default Index Method
     */
    public function index(){

        //Set Category Query Options
        $options = array(
            'order' => array('Category.name' => 'asc')
        );

        //Get Categories
        $categories = $this->Job->Category->find('all', $options);

        //Set Categories
        $this->set('categories', $categories);

        //Set Query Options
        $options = array(
            'order' => array('Job.created' => 'DESC'),
            'limit' => 10
        );

        //Get Job Info
        $jobs = $this->Job->find('all', $options); //The Find Method is getting the Jobs from the DB

        //Set Title
        $this->set('title_for_layout', 'JobFinds | Welcome');

        $this->set('jobs', $jobs);
    }



    /*
    * Browse Method
    */
    public function browse($category = null) //I want the option to either browse with an id or just browse
    {
        //Init Conditions Array
        $conditions = array();

        //Check Keyword Filter
        if($this->request->is('post'))
        {
            if(!empty($this->request->data('keywords'))) //This is pretty much as grabbing the $_POST['keywords'] variable
            {
                $conditions[] =  array('OR' => array( //Without the 'OR' the default is 'AND'
                    'Job.title LIKE' => "%" . $this->request->data('keywords') . "%",
                    'Job.description LIKE' => "%" . $this->request->data('keywords') . "%"
                ));
            }
        }

        //State Filter
        if(!empty($this->request->data('state')) && $this->request->data('state') != 'Select State')
        {
            //Match State
            $conditions[] =  array(
                'Job.state LIKE' => "%" . $this->request->data('state') . "%"
            );
        }

        //Category Filter
        if(!empty($this->request->data('category')) && $this->request->data('category') != 'Select Category')
        {
            //Match Category
            $conditions[] =  array(
                'Job.category_id LIKE' => "%" . $this->request->data('category') . "%"
            );
        }

        //Set Category Query Options
        $options = array(
            'order' => array('Category.name' => 'asc')
        );

        //Get Categories
        $categories = $this->Job->Category->find('all', $options);

        //Set Categories
        $this->set('categories', $categories);

        if($category != null)
        {
            //Match Category
            $conditions[] =  array(
                'Job.category_id LIKE' => "%" . $category . "%"
            );
        }

        //Set Query Options
        $options = array(
            'order' => array('Job.created' => 'desc'),
            'conditions' => $conditions,
            'limit' => 8
        );

        //Get Job Info
        $jobs = $this->Job->find('all', $options);

        //Set Title
        $this->set('title_for_layout', 'JobFinds | Browse For A Job');

        $this->set('jobs', $jobs);
    }


    /*
     * View Single Jobs
     */
    public function view($id)
    {
        if(!$id)
        {
            throw new NotFoundException(__('Invalid job listing'));
        }

        $job = $this->Job->findById($id);

        if(!$job)
        {
            throw new NotFoundException(__('Invalid job listing'));
        }

        //Set Title
        $this->set('title_for_layout', $job['Job']['title']);

        $this->set('job', $job);
    }


    /*
	   * Add Job
	   */
    public function add()
    {
        //Get Categories for select list
        $options = array(
            'order' => array('Category.name' => 'asc')
        );

        //Get Categories
        $categories = $this->Job->Category->find('list', $options);

        //Set Categories
        $this->set('categories', $categories);

        //Get types for select list
        $types = $this->Job->Type->find('list');

        //Set Types
        $this->set('types', $types);

        if($this->request->is('post'))
        {
            $this->Job->create();

            //Save Logged User Id
            $this->request->data['Job']['user_id'] = 1;

            if($this->Job->save($this->request->data))
            {
                $this->Session->setFlash(__('Your job has been listed'));
                return $this->redirect(array('action' => 'index'));
            }

            $this->Session->setFlash(__('Unable to add your job'));
        }
    }


    /*
     * Edit Job
     */
    public function edit($id)
    {
        //Get Categories for select list
        $options = array(
            'order' => array('Category.name' => 'asc')
        );

        //Get Categories
        $categories = $this->Job->Category->find('list', $options);

        //Set Categories
        $this->set('categories', $categories);

        //Get types for select list
        $types = $this->Job->Type->find('list');

        //Set Types
        $this->set('types', $types);

        if(!$id)
        {
            throw new NotFoundException(__('Invalid job listing'));
        }

        $job = $this->Job->findById($id);

        if(!$job)
        {
            throw new NotFoundException(__('Invalid job listing'));
        }

        if($this->request->is(array('job', 'put'))) //It is an array because I want to tell the object as well, not just the type as done previously
        {
            $this->Job->id = $id;

            if($this->Job->save($this->request->data)){
                $this->Session->setFlash(__('Your job has been updated'));
                return $this->redirect(array('action' => 'index'));
            }

            $this->Session->setFlash(__('Unable to update your job'));
        }

        if(!$this->request->data)
        {
            $this->request->data = $job; //Pre Fill the form
        }
    }


    /*
     * Delete a Job
     */
    public function delete($id)
    {
        if ($this->request->is('get'))
        {
            throw new MethodNotAllowedException();
        }

        if ($this->Job->delete($id))
        {
            $this->Session->setFlash(
                __('The job with id: %s has been deleted.', h($id))
            );
            return $this->redirect(array('action' => 'index'));
        }
    }

}