<?php
        public function index()
    {
        $categories = Categories::all();
        $articales = Categories::all();
        // $categories = Categories::all();
        return view('articales.index',compact('categories','articales'));
    }

    public function create()
    {

        return view('articales.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required|exists:category,id',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('article_images', $imageName, 'public');
        }

        Articale::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'image' => $imagePath,
            'category_id' => $request->input('category_id'),
        ]);

        return redirect()->route('articales.index')->with('success', 'Article created successfully');
    }

    public function edit(Articale $article)
    {
        $categories = Categories::all();
        return view('articales.edit', compact('article', 'categories'));
    }


    public function destroy(Articale $article)
    {
        $article->delete();

        return redirect()->route('articales.index')->with('success', 'Article deleted successfully');
    }
  
?>


